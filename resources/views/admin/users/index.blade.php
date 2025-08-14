<!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <title>Nutzer verwalten</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">Nutzer verwalten</h1>
    <a class="btn btn-outline-secondary" href="/admin">Zurück</a>
  </div>

  @if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif
  @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title">Einzeln einladen</h5>
          <form method="POST" action="/admin/users">
            @csrf
            <div class="mb-2">
              <label class="form-label">Name</label>
              <input class="form-control" name="name" required>
            </div>
            <div class="mb-2">
              <label class="form-label">E‑Mail (optional)</label>
              <input class="form-control" name="email" type="email" placeholder="max@example.com">
            </div>
            <div class="form-check mb-3">
              <input type="hidden" name="send" value="0">
              <input class="form-check-input" type="checkbox" name="send" id="send1" value="1" checked>
              <label class="form-check-label" for="send1">Einladung per E‑Mail senden</label>
            </div>
            <button class="btn btn-primary">Anlegen</button>
        </div>
      </div>
    </div>

    <!--<div class="col-md-6">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title">CSV‑Import</h5>
          <p class="text-muted">CSV‑Header: <code>name,email</code></p>
          <form method="POST" action="/admin/users/import" enctype="multipart/form-data">
            @csrf
            <div class="mb-2">
              <input class="form-control" type="file" name="csv" accept=".csv,text/csv" required>
            </div>
            <div class="form-check mb-3">
              <input type="hidden" name="send" value="0">
              <input class="form-check-input" type="checkbox" name="send" id="send2" value="1" checked>
              <label class="form-check-label" for="send2">Einladungen per E‑Mail senden</label>
            </div>
            <button class="btn btn-primary">Import starten</button>
          </form>
        </div>
      </div>
    </div>
  </div>-->

  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Alle Nutzer</h5>
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th>Name</th>
              <th>E‑Mail</th>
              <th>GUID</th>
              <th>Link</th>
              <th class="text-end">Aktion</th>
            </tr>
          </thead>
          <tbody>
          @foreach($users as $u)
            <tr>
              <td>{{ $u->name }}</td>
              <td>{{ $u->email }}</td>
              <td><code style="font-size:12px">{{ $u->guid }}</code></td>
              <td><a href="/dashboard/{{ $u->guid }}" target="_blank">öffnen</a></td>
              <td class="text-end">
                <form method="POST" action="/admin/users/resend/{{ $u->id }}" class="d-inline">
                  @csrf
                  <button class="btn btn-sm btn-outline-primary" {{ $u->email ? '' : 'disabled' }}>Einladung erneut</button>
                </form>
                <form method="POST" action="/admin/users/{{ $u->id }}" class="d-inline" onsubmit="return confirm('Diesen Nutzer löschen?');">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Löschen</button>
                </form>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</body>
</html>
