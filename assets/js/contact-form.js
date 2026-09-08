/*
 * [zoneplay_contact_form] — submit handler.
 *
 * Posts the form to admin-ajax.php (action + nonce come from wp_localize_script
 * as window.ZP_CF; the nonce field rides in the form's own FormData). On
 * success it swaps the form for the confirmation panel; on failure it shows
 * the error line. Progressive: with JS off the form still renders, it just
 * won't submit (no `action` attribute).
 *
 * The <form> has no `novalidate`, so the browser's native `required` /
 * `type="email"` check runs first — the `submit` event (and this handler)
 * only fires once the fields are valid. reportValidity() below is a
 * belt-and-braces guard for programmatic submits.
 */
( function () {
	'use strict';

	var cfg = window.ZP_CF || {};
	var form = document.getElementById( 'zpcf-form' );
	var success = document.getElementById( 'zpcf-success' );
	var errorEl = document.getElementById( 'zpcf-error' );
	var submitBtn = document.getElementById( 'zpcf-submit' );
	var resetBtn = document.getElementById( 'zpcf-reset' );

	if ( ! form || ! cfg.ajaxUrl || ! cfg.action ) {
		return;
	}

	var btnLabel = submitBtn ? submitBtn.querySelector( 'span' ) : null;
	var btnLabelText = btnLabel ? btnLabel.textContent : '';

	function showError( msg ) {
		if ( ! errorEl ) {
			return;
		}
		errorEl.textContent = msg;
		errorEl.hidden = false;
	}

	function clearError() {
		if ( errorEl ) {
			errorEl.hidden = true;
			errorEl.textContent = '';
		}
	}

	form.addEventListener( 'submit', function ( e ) {
		e.preventDefault();

		if ( typeof form.reportValidity === 'function' && ! form.reportValidity() ) {
			return; // native bubbles are showing; nothing to send
		}

		clearError();

		if ( submitBtn ) {
			submitBtn.disabled = true;
		}
		if ( btnLabel ) {
			btnLabel.textContent = 'Sending…';
		}

		var data = new FormData( form );
		data.append( 'action', cfg.action );

		fetch( cfg.ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' } )
			.then( function ( res ) {
				return res.json().catch( function () {
					return { ok: false };
				} );
			} )
			.then( function ( json ) {
				if ( json && json.ok ) {
					form.reset();
					form.hidden = true;
					if ( success ) {
						success.hidden = false;
					}
				} else {
					showError( ( json && json.error ) || 'Something went wrong. Please try again.' );
				}
			} )
			.catch( function () {
				showError( 'Could not reach the server. Please try again, or email us directly.' );
			} )
			.then( function () {
				if ( submitBtn ) {
					submitBtn.disabled = false;
				}
				if ( btnLabel ) {
					btnLabel.textContent = btnLabelText;
				}
			} );
	} );

	if ( resetBtn ) {
		resetBtn.addEventListener( 'click', function () {
			if ( success ) {
				success.hidden = true;
			}
			form.hidden = false;
			clearError();
		} );
	}
} )();
