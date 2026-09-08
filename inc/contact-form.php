<?php
/**
 * Standalone contact form — the [zoneplay_contact_form] shortcode.
 *
 * This is NOT theme chrome. It's a self-contained enquiry form, ported from
 * the Astro build's `public/send-mail.php` (contact branch): same fields,
 * same validation, same table-based inline-styled HTML email. It sends via
 * core `wp_mail()`, which the FluentSMTP plugin already routes through SMTP —
 * so no PHPMailer bundle and no Contact Form 7. Its CSS/JS load only on
 * pages that actually contain the shortcode.
 *
 * Everything lives here on purpose: shortcode markup, asset registration,
 * the AJAX handler, and the email builder. Kept out of functions.php / the
 * inc/ theme files because it's a bolt-on feature, not part of rendering
 * the site.
 *
 * @package ZonePlay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const ZP_CF_SHORTCODE = 'zoneplay_contact_form';
const ZP_CF_ACTION    = 'zoneplay_contact_submit';
const ZP_CF_NONCE     = 'zoneplay_contact_form';
const ZP_CF_HANDLE    = 'zoneplay-contact-form';

/**
 * Enquiry-type <select> options: value => human label. The labels are also
 * reused to build the email subject / body, matching the Astro handler.
 */
function zp_cf_enquiry_options() {
	return array(
		''               => __( 'Select a topic', 'zoneplay' ),
		'softplay'       => __( 'Soft Play / General', 'zoneplay' ),
		'parties'        => __( 'Birthday Parties', 'zoneplay' ),
		'membership'     => __( 'Membership', 'zoneplay' ),
		'exclusive-hire' => __( 'Exclusive Hire', 'zoneplay' ),
		'summer-pass'    => __( 'Summer Pass', 'zoneplay' ),
		'other'          => __( 'Other', 'zoneplay' ),
	);
}

/**
 * Where enquiries are delivered. Filterable; defaults to the site admin
 * email (which on this install is the same address the Astro form used).
 */
function zp_cf_recipient() {
	return apply_filters( 'zoneplay_contact_form_recipient', get_option( 'admin_email' ) );
}

/* -------------------------------------------------------------------------
 * Assets — registered always, enqueued only where the shortcode is used.
 * ---------------------------------------------------------------------- */

add_action( 'wp_enqueue_scripts', 'zp_cf_register_assets' );

function zp_cf_register_assets() {
	wp_register_style(
		ZP_CF_HANDLE,
		ZP_THEME_URI . '/assets/css/contact-form.css',
		array( 'zoneplay-fonts' ),
		zp_asset_ver( 'assets/css/contact-form.css' )
	);

	wp_register_script(
		ZP_CF_HANDLE,
		ZP_THEME_URI . '/assets/js/contact-form.js',
		array(),
		zp_asset_ver( 'assets/js/contact-form.js' ),
		array( 'in_footer' => true )
	);

	wp_localize_script(
		ZP_CF_HANDLE,
		'ZP_CF',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'action'  => ZP_CF_ACTION,
		)
	);

	// Enqueue up-front when the queried singular page embeds the shortcode
	// (works even when it's nested inside an Editable HTML block, since
	// has_shortcode() scans the raw post_content string). The shortcode
	// callback also enqueues, as a fallback for widgets / other contexts.
	if ( is_singular() ) {
		$post = get_post();
		if ( $post instanceof WP_Post && has_shortcode( $post->post_content, ZP_CF_SHORTCODE ) ) {
			wp_enqueue_style( ZP_CF_HANDLE );
			wp_enqueue_script( ZP_CF_HANDLE );
		}
	}
}

/* -------------------------------------------------------------------------
 * Shortcode
 * ---------------------------------------------------------------------- */

add_shortcode( ZP_CF_SHORTCODE, 'zp_cf_render' );

/**
 * @param array|string $atts
 *   button_text  Submit button label. Default "Send Message".
 *   success_text Confirmation paragraph after a successful send.
 */
function zp_cf_render( $atts ) {
	$atts = shortcode_atts(
		array(
			'button_text'  => __( 'Send Message', 'zoneplay' ),
			'success_text' => __( "Thanks for getting in touch — we'll be back to you as soon as we can.", 'zoneplay' ),
		),
		$atts,
		ZP_CF_SHORTCODE
	);

	wp_enqueue_style( ZP_CF_HANDLE );
	wp_enqueue_script( ZP_CF_HANDLE );

	$options = zp_cf_enquiry_options();

	// lucide "send" + "circle-check-big", inline so no sprite dependency.
	$icon_send  = '<svg class="zpcf__submit-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"/><path d="m21.854 2.147-10.94 10.939"/></svg>';
	$icon_check = '<svg class="zpcf__success-icon" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21.801 10A10 10 0 1 1 17 3.335"/><path d="m9 11 3 3L22 4"/></svg>';

	ob_start();
	?>
	<div class="zpcf">
		<div class="zpcf__success" id="zpcf-success" hidden>
			<?php echo $icon_check; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static markup. ?>
			<p class="zpcf__success-title"><?php esc_html_e( 'Message sent!', 'zoneplay' ); ?></p>
			<p class="zpcf__success-text"><?php echo esc_html( $atts['success_text'] ); ?></p>
			<button type="button" class="zpcf__reset" id="zpcf-reset"><?php esc_html_e( 'Send another message', 'zoneplay' ); ?></button>
		</div>

		<form class="zpcf__form" id="zpcf-form">
			<?php wp_nonce_field( ZP_CF_NONCE, 'zpcf_nonce' ); ?>

			<div class="zpcf__hp" aria-hidden="true">
				<label><?php esc_html_e( 'Leave this field empty', 'zoneplay' ); ?>
					<input type="text" name="website" tabindex="-1" autocomplete="off" value="">
				</label>
			</div>

			<div class="zpcf__row">
				<div class="zpcf__field">
					<label for="zpcf-name"><?php esc_html_e( 'Your Name', 'zoneplay' ); ?> <span class="zpcf__req">*</span></label>
					<input id="zpcf-name" type="text" name="name" required autocomplete="name" placeholder="<?php esc_attr_e( 'e.g. John Doe', 'zoneplay' ); ?>">
				</div>
				<div class="zpcf__field">
					<label for="zpcf-email"><?php esc_html_e( 'Email Address', 'zoneplay' ); ?> <span class="zpcf__req">*</span></label>
					<input id="zpcf-email" type="email" name="email" required autocomplete="email" placeholder="hello@example.com">
				</div>
			</div>

			<div class="zpcf__row">
				<div class="zpcf__field">
					<label for="zpcf-phone"><?php esc_html_e( 'Phone Number', 'zoneplay' ); ?> <span class="zpcf__opt">(<?php esc_html_e( 'optional', 'zoneplay' ); ?>)</span></label>
					<input id="zpcf-phone" type="tel" name="phone" autocomplete="tel" placeholder="07700 900000">
				</div>
				<div class="zpcf__field">
					<label for="zpcf-enquiry"><?php esc_html_e( "What's your enquiry about?", 'zoneplay' ); ?></label>
					<select id="zpcf-enquiry" name="enquiry">
						<?php foreach ( $options as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<div class="zpcf__field">
				<label for="zpcf-message"><?php esc_html_e( 'Message', 'zoneplay' ); ?> <span class="zpcf__req">*</span></label>
				<textarea id="zpcf-message" name="message" rows="6" required placeholder="<?php esc_attr_e( 'How can we help?', 'zoneplay' ); ?>"></textarea>
			</div>

			<p class="zpcf__error" id="zpcf-error" role="alert" hidden></p>

			<button type="submit" class="zpcf__submit" id="zpcf-submit">
				<?php echo $icon_send; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static markup. ?>
				<span><?php echo esc_html( $atts['button_text'] ); ?></span>
			</button>
		</form>
	</div>
	<?php
	return ob_get_clean();
}

/* -------------------------------------------------------------------------
 * AJAX handler — validate, send, respond JSON. HTTP 200 always; the JSON
 * `ok` flag carries success/failure (matches the Astro contract).
 * ---------------------------------------------------------------------- */

add_action( 'wp_ajax_' . ZP_CF_ACTION, 'zp_cf_handle_submit' );
add_action( 'wp_ajax_nopriv_' . ZP_CF_ACTION, 'zp_cf_handle_submit' );

function zp_cf_handle_submit() {
	// The nonce field (name="zpcf_nonce") rides along in the form's FormData.
	if ( ! check_ajax_referer( ZP_CF_NONCE, 'zpcf_nonce', false ) ) {
		wp_send_json( array( 'ok' => false, 'error' => __( 'Your session expired. Please refresh the page and try again.', 'zoneplay' ) ) );
	}

	// Honeypot — a bot filled the hidden field. Pretend success, send nothing.
	if ( '' !== trim( (string) wp_unslash( $_POST['website'] ?? '' ) ) ) {
		wp_send_json( array( 'ok' => true ) );
	}

	$name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$phone   = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
	$enquiry = sanitize_key( wp_unslash( $_POST['enquiry'] ?? '' ) );
	$message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );

	if ( '' === $name || '' === $message || ! is_email( $email ) ) {
		wp_send_json( array( 'ok' => false, 'error' => __( 'Please provide your name, a valid email, and a message.', 'zoneplay' ) ) );
	}

	$labels        = zp_cf_enquiry_options();
	$enquiry_label = ( '' !== $enquiry && isset( $labels[ $enquiry ] ) ) ? $labels[ $enquiry ] : __( 'General enquiry', 'zoneplay' );

	$subject = sprintf( __( 'New enquiry: %s', 'zoneplay' ), $enquiry_label );
	$body    = zp_cf_email_html( $name, $email, $phone, $enquiry_label, $message );
	$headers = array(
		'Content-Type: text/html; charset=UTF-8',
		sprintf( 'Reply-To: %s <%s>', preg_replace( '/[\r\n]+/', ' ', $name ), $email ),
	);

	$sent = wp_mail( zp_cf_recipient(), $subject, $body, $headers );

	if ( $sent ) {
		wp_send_json( array( 'ok' => true ) );
	}

	wp_send_json( array( 'ok' => false, 'error' => __( 'Sorry, your message could not be sent. Please try again or email us directly.', 'zoneplay' ) ) );
}

/* -------------------------------------------------------------------------
 * HTML email — a table-based, fully inline-styled shell. Ported verbatim
 * (structure + styles) from the Astro send-mail.php emailShell/badgeHtml/
 * detailRowsHtml/messageBoxHtml helpers so delivered mail looks identical.
 * No <style> blocks / flexbox / CSS vars — Outlook's Word engine ignores
 * all of those.
 * ---------------------------------------------------------------------- */

/**
 * Build the complete HTML email body for one enquiry.
 */
function zp_cf_email_html( $name, $email, $phone, $enquiry_label, $message ) {
	$accent  = '#F98711'; // zp-orange
	$phone   = '' !== $phone ? $phone : __( 'Not provided', 'zoneplay' );
	$msg_esc = nl2br( esc_html( $message ) );

	$content =
		zp_cf_email_badge( __( 'Enquiry Type', 'zoneplay' ), $enquiry_label, '#FEF0DE', '#F98711', '#8A4B0A' ) .
		zp_cf_email_rows(
			array(
				__( 'Name', 'zoneplay' )  => $name,
				__( 'Email', 'zoneplay' ) => $email,
				__( 'Phone', 'zoneplay' ) => $phone,
			)
		) .
		zp_cf_email_message_box( __( 'Message', 'zoneplay' ), $msg_esc );

	return zp_cf_email_shell( $accent, 'ZONE PLAY CARDIFF', __( 'New Website Enquiry', 'zoneplay' ), $content );
}

/** Outer shell: coloured header, body slot, footer with NAP. */
function zp_cf_email_shell( $accent, $eyebrow, $heading, $body_html ) {
	$eyebrow = esc_html( $eyebrow );
	$heading = esc_html( $heading );
	$accent  = esc_attr( $accent );

	return <<<HTML
<!DOCTYPE html>
<html lang="en-GB">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="color-scheme" content="light">
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f1f5f9;">
<tr>
<td align="center" style="padding:32px 16px;">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;background-color:#ffffff;border:1px solid #e2e8f0;border-radius:16px;">
<tr>
<td style="background-color:{$accent};padding:28px 32px;text-align:center;border-radius:16px 16px 0 0;">
<p style="margin:0;color:#ffffff;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;">{$eyebrow}</p>
<p style="margin:6px 0 0;color:#ffffff;font-family:Arial,Helvetica,sans-serif;font-size:24px;font-weight:800;">{$heading}</p>
</td>
</tr>
<tr>
<td style="padding:32px;font-family:Arial,Helvetica,sans-serif;color:#334155;font-size:15px;line-height:1.6;">
{$body_html}
</td>
</tr>
<tr>
<td style="padding:18px 32px;background-color:#f8fafc;border-top:1px solid #e2e8f0;border-radius:0 0 16px 16px;text-align:center;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#94a3b8;">
Zone Play Cardiff &middot; Unit 5, Stadium Close, Cardiff CF11 8TS<br>
<a href="mailto:info@zoneplaycardiff.co.uk" style="color:#0082C8;text-decoration:none;">info@zoneplaycardiff.co.uk</a> &middot; 02920 239777
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
HTML;
}

/** Highlighted callout — used for the enquiry type. */
function zp_cf_email_badge( $label, $value, $bg, $border, $label_color ) {
	$label       = esc_html( $label );
	$value       = esc_html( $value );
	$bg          = esc_attr( $bg );
	$border      = esc_attr( $border );
	$label_color = esc_attr( $label_color );

	return <<<HTML
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 20px;">
<tr>
<td style="background-color:{$bg};border:2px solid {$border};border-radius:12px;padding:14px 20px;">
<p style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:700;color:{$label_color};text-transform:uppercase;letter-spacing:0.5px;">{$label}</p>
<p style="margin:4px 0 0;font-family:Arial,Helvetica,sans-serif;font-size:20px;font-weight:800;color:#0E355D;">{$value}</p>
</td>
</tr>
</table>
HTML;
}

/** Label:value rows in a bordered box — the sender's contact details. */
function zp_cf_email_rows( array $rows ) {
	$count = count( $rows );
	$out   = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #e2e8f0;border-radius:12px;margin:0 0 20px;">';
	$i     = 0;
	foreach ( $rows as $label => $value ) {
		$i++;
		$rule  = $i < $count ? 'border-bottom:1px solid #e2e8f0;' : '';
		$label = esc_html( $label );
		$value = esc_html( $value );
		$out  .= "<tr><td style=\"padding:10px 16px;{$rule}font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#334155;\"><strong style=\"color:#0E355D;\">{$label}:</strong> {$value}</td></tr>";
	}
	$out .= '</table>';
	return $out;
}

/** Bordered box for free text — the enquiry message. Content is pre-escaped. */
function zp_cf_email_message_box( $label, $safe_html_content ) {
	$label = esc_html( $label );

	return <<<HTML
<p style="margin:0 0 10px;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:700;color:#0E355D;text-transform:uppercase;letter-spacing:0.5px;">{$label}</p>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #e2e8f0;border-radius:12px;">
<tr>
<td style="padding:16px;background-color:#f8fafc;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.6;color:#334155;">{$safe_html_content}</td>
</tr>
</table>
HTML;
}
