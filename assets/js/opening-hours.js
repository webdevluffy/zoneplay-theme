(function () {
  var lists = document.querySelectorAll('.js-opening-hours');
  if (!lists.length) return;

  var DAY_NAMES = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

  // KEEP IN SYNC WITH src/lib/opening-hours.ts (groupOpeningTimes/formatTime12h).
  function signature(day) {
    return day.closed ? 'closed' : day.opens + '-' + day.closes;
  }

  function groupOpeningTimes(days) {
    var groups = [];
    for (var i = 0; i < days.length; i++) {
      var day = days[i];
      var prev = groups[groups.length - 1];
      if (prev && signature(days[prev.lastDayIdx]) === signature(day)) {
        prev.lastDayIdx = i;
      } else {
        groups.push({ firstDayIdx: i, lastDayIdx: i, opens: day.opens, closes: day.closes, closed: day.closed });
      }
    }
    return groups;
  }

  function formatTime12h(time) {
    var parts = time.split(':');
    var h = Number(parts[0]);
    var m = parts[1];
    var period = h < 12 ? 'am' : 'pm';
    var h12 = h % 12 === 0 ? 12 : h % 12;
    return m === '00' ? h12 + period : h12 + ':' + m + period;
  }

  function groupLabel(group) {
    return group.firstDayIdx === group.lastDayIdx
      ? DAY_NAMES[group.firstDayIdx]
      : DAY_NAMES[group.firstDayIdx] + ' – ' + DAY_NAMES[group.lastDayIdx];
  }

  function toDisplayRows(days) {
    return groupOpeningTimes(days).map(function (group) {
      return {
        day: groupLabel(group),
        hours: group.closed ? 'Closed' : formatTime12h(group.opens) + ' – ' + formatTime12h(group.closes),
        closed: group.closed,
      };
    });
  }

  function isValidDayHours(row) {
    return row && typeof row === 'object' && typeof row.closed === 'boolean' && (row.closed || (typeof row.opens === 'string' && typeof row.closes === 'string'));
  }

  // Capture each list's first <li> as a style template before mutating anything,
  // so rebuilt rows keep this page's existing markup/classes untouched.
  var templates = [];
  lists.forEach(function (list, listIndex) {
    var firstLi = list.querySelector('li');
    templates[listIndex] = firstLi ? firstLi.cloneNode(true) : null;
  });

  fetch('/data/opening-hours.json', { cache: 'no-store' })
    .then(function (res) {
      return res.ok ? res.json() : null;
    })
    .then(function (data) {
      if (!Array.isArray(data) || data.length !== 7 || !data.every(isValidDayHours)) return;

      var rows = toDisplayRows(data);

      lists.forEach(function (list, listIndex) {
        var template = templates[listIndex];
        if (!template) return;

        var openColor = list.dataset.colorOpen || '';
        var closedColor = list.dataset.colorClosed || '#E12128';

        var fragment = document.createDocumentFragment();
        rows.forEach(function (row, i) {
          var li = template.cloneNode(true);
          li.setAttribute('data-row', String(i));

          var daySpan = li.querySelector('[data-field="day"]');
          if (daySpan) daySpan.textContent = row.day;

          var hoursSpan = li.querySelector('[data-field="hours"]');
          if (hoursSpan) {
            hoursSpan.textContent = row.hours;
            hoursSpan.style.color = row.closed ? closedColor : openColor;
          }

          fragment.appendChild(li);
        });

        list.replaceChildren(fragment);
      });
    })
    .catch(function () {
      // Fetch failed — leave the build-time hours already rendered as-is.
    });
})();
