<!doctype html><html lang="de"><head>
<meta charset="utf-8"><title>Schicht anlegen</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="p-4">
<div class="container">
  <h1>Schicht anlegen</h1>
  @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif
  <form method="POST" action="/admin/shifts">@csrf
    <div class="mb-3"><label class="form-label">Name</label>
      <input class="form-control" name="name" required></div>
    <div class="mb-3"><label class="form-label">Farbe (optional)</label>
      <input class="form-control form-control-color" type="color" name="color" value="#007bff">
      <small class="text-muted">Hex, z. B. #007bff</small>
    </div>
    <button class="btn btn-primary">Speichern</button>
    <a href="/admin/shifts" class="btn btn-outline-secondary">Abbrechen</a>
  </form>
</div></body></html>
