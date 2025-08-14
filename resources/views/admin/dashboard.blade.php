<!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FullCalendar CDN (falls noch nicht auf der Seite) -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.global.min.js"></script>
</head>
<body class="p-4">
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">Admin‑Dashboard</h1>
    <form method="POST" action="/admin/logout">
      @csrf
      <button class="btn btn-outline-secondary btn-sm">Logout</button>
    </form>
  </div>

  @if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif



<div class="card mb-4">
  <div class="card-body">
    <h5 class="card-title mb-3">Gesamter Kalender (Admin)</h5>
    <div id="adminCalendar"></div>

    <div class="row g-2 mt-3">
      <div class="col-md-3"><input type="date" id="admDate" class="form-control"></div>
      <div class="col-md-3">
        <select id="admShift" class="form-select">
          @foreach($shifts as $s)
            <option value="{{ $s->id }}">{{ $s->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <select id="admUser" class="form-select">
          @foreach(\App\Models\User::orderBy('name')->get() as $u)
            <option value="{{ $u->id }}">{{ $u->name }} {{ $u->email ? '('.$u->email.')' : '' }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 d-grid">
        <button id="admAddBtn" class="btn btn-primary">Eintragen</button>
      </div>
    </div>
    <small class="text-muted">Klick auf Termin → löschen</small>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const calEl = document.getElementById('adminCalendar');

  const adminCal = new FullCalendar.Calendar(calEl, {
    locale: 'de',
    firstDay: 1,
    height: 'auto',
    initialView: (window.innerWidth < 576) ? 'listWeek' : 'dayGridMonth',
    headerToolbar: { left:'prev,next today', center:'title', right:'dayGridMonth,timeGridWeek,timeGridDay,listWeek' },

    // *** WICHTIG: Cookies/Sitzung mitsenden, sonst 302 -> Login ***
    fetchOptions: { credentials: 'same-origin' },

    events: {
      url: '/admin/calendar/events',
      method: 'GET'
    },

    eventClick: function(info){
      if (confirm('Buchung löschen?')) {
        fetch('/admin/calendar/' + info.event.id, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          credentials: 'same-origin',
          body: new URLSearchParams({ _token: csrf })
        }).then(()=> adminCal.refetchEvents());
      }
    }
  });

  adminCal.render();

  document.getElementById('admAddBtn').addEventListener('click', function(){
    const date = document.getElementById('admDate').value;
    const shift_id = document.getElementById('admShift').value;
    const user_id = document.getElementById('admUser').value;
    if (!date || !shift_id || !user_id) { alert('Bitte Datum, Schicht und Nutzer wählen.'); return; }

    fetch('/admin/calendar/book', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin',
      body: new URLSearchParams({ _token: csrf, date, shift_id, user_id })
    }).then(async (res)=>{
      if (!res.ok) { alert(await res.text()); return; }
      adminCal.refetchEvents();
    });
  });
});
</script>



  <div class="row g-3">
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title">Ferien verwalten</h5>
          <p class="card-text">Anlegen, bearbeiten und löschen von Ferien.</p>
          <a class="btn btn-primary" href="/admin/holidays">Öffnen</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title">Wochen‑PDF</h5>
          <form class="row g-2" method="GET" action="/admin/export/week">
            <div class="col-12">
              <label class="form-label">Datum in gewünschter Woche</label>
              <input type="date" name="week" class="form-control">
            </div>
            <div class="col-12">
              <button class="btn btn-primary w-100">PDF herunterladen</button>
            </div>
          </form>
          <small class="text-muted">Ohne Datum → aktuelle Woche.</small>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title">CSV‑Export (Zeitraum)</h5>
          <form class="row g-2" method="GET" action="/admin/export/csv">
            <div class="col-6">
              <label class="form-label">Von</label>
              <input type="date" name="from" class="form-control">
            </div>
            <div class="col-6">
              <label class="form-label">Bis</label>
              <input type="date" name="to" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label">Ort</label>
              <select class="form-select" name="shift_id">
                <option value="">Alle</option>
                @if(isset($shifts) && count($shifts))
                  @foreach($shifts as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                  @endforeach
                @else
                  {{-- Fallback, falls Controller mal nichts liefert --}}
                  <option value="1">Früh</option>
                  <option value="2">Mittel</option>
                  <option value="3">Spät</option>
                @endif
              </select>
            </div>
            <div class="col-12">
              <button class="btn btn-primary w-100">CSV herunterladen</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title">Nutzer einladen</h5>
          <p class="card-text">Einzel-Anlage, CSV-Import, Einladung erneut senden.</p>
          <a class="btn btn-primary" href="/admin/users">Öffnen</a>
        </div>
      </div>
    </div>


    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title">CSV pro Nutzer</h5>
          <form class="row g-2" method="GET" action="/admin/export/user-csv">
            <div class="col-12">
              <label class="form-label">Nutzer‑GUID</label>
              <input type="text" name="guid" class="form-control" placeholder="z.B. 550e8400-e29b-41d4-a716-446655440000" required>
            </div>
            <div class="col-12">
              <button class="btn btn-primary w-100">CSV herunterladen</button>
            </div>
          </form>
          <small class="text-muted">GUID erhältst du über den Invite‑Command oder aus der Datenbank.</small>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card h-100"><div class="card-body">
        <h5 class="card-title">Schichten konfigurieren</h5>
        <p class="card-text">Namen und Farben verwalten.</p>
        <a class="btn btn-primary" href="/admin/shifts">Öffnen</a>
      </div></div>
    </div>


  </div>
</div>
</body>
</html>
