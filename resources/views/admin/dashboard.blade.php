<!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
              <label class="form-label">Schicht (optional)</label>
              <select class="form-select" name="shift_id">
                <option value="">Alle</option>
                <option value="1">Früh</option>
                <option value="2">Mittel</option>
                <option value="3">Spät</option>
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

  </div>
</div>
</body>
</html>
