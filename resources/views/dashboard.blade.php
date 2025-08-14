<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Schülerlotsen Kalender</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- FullCalendar -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.global.min.js"></script>

  <style> body { margin: 20px; } </style>
</head>
<body>
<div class="container">
  <h1 class="mb-3">Willkommen, {{ $user->name }}</h1>
  <div id="calendar"></div>
</div>

<!-- Modal -->
<div class="modal fade" id="shiftModal" tabindex="-1" aria-labelledby="shiftModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="shiftModalLabel">Schicht wählen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
      </div>
      <div class="modal-body">
        <select id="shiftSelect" class="form-select">
          <option value="">-- bitte wählen --</option>
          <option value="1">Früh</option>
          <option value="2">Mittel</option>
          <option value="3">Spät</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
        <button type="button" id="saveShiftBtn" class="btn btn-primary">Buchen</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendar');
  const guid = "{{ $user->guid }}";
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  let selectedDate = null;

  // --- Kalender initialisieren ---
  const calendar = new FullCalendar.Calendar(calendarEl, {
    locale: 'de',
    firstDay: 1,
    height: 'auto',
    dayMaxEvents: true,
    nowIndicator: true,
    weekends: false,
    businessHours: { daysOfWeek: [1,2,3,4,5], startTime: '07:00', endTime: '18:00' },
    initialView: (window.innerWidth < 576) ? 'listWeek' : 'dayGridMonth',
    headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' },
    buttonText: { today: 'Heute', month: 'Monat', week: 'Woche', day: 'Tag', list: 'Liste' },

    eventSources: [
      { url: '/calendar/' + guid, method: 'GET' },
      { url: '/calendar/' + guid + '/holidays', method: 'GET' }
    ],

    eventClick: function(info) {
      if (info.event.extendedProps && info.event.extendedProps.user_id === {{ $user->id }}) {
        if (confirm('Buchung löschen?')) {
          fetch('/booking/' + guid + '/' + info.event.id, {
            method: 'DELETE',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: new URLSearchParams({ _token: csrf })
          }).then(() => calendar.refetchEvents());
        }
      }
    },

    dateClick: function(info) {
      // Ferien (background) blockieren
      const isHoliday = calendar.getEvents().some(e =>
        e.display === 'background' &&
        info.date >= e.start && (e.end ? info.date < e.end : true)
      );
      if (isHoliday) { alert('An Ferientagen sind keine Buchungen möglich.'); return; }

      selectedDate = info.dateStr;
      new bootstrap.Modal(document.getElementById('shiftModal')).show();
    }
  });

  // --- Buchung speichern ---
  document.getElementById('saveShiftBtn').addEventListener('click', function () {
    const shiftId = document.getElementById('shiftSelect').value;
    if (!shiftId) { alert('Bitte eine Schicht auswählen.'); return; }

    // Client-Check: gleiche Schicht am selben Tag schon belegt?
    const sameDaySameShift = calendar.getEvents().some(e =>
      e.extendedProps && e.extendedProps.shift_id == shiftId && e.startStr === selectedDate
    );
    if (sameDaySameShift) { alert('Diese Schicht ist an diesem Tag bereits belegt.'); return; }

    fetch('/booking/' + guid, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin',
      body: new URLSearchParams({
        _token: csrf,
        date: selectedDate,
        shift_id: shiftId
      })
    }).then(async (res) => {
      if (!res.ok) { alert(await res.text() || 'Buchung fehlgeschlagen'); return; }
      bootstrap.Modal.getInstance(document.getElementById('shiftModal')).hide();
      calendar.refetchEvents();
    });
  });

  calendar.render();
});
</script>
</body>
</html>
