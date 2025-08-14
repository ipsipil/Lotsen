<!doctype html><html lang="de"><head>
<meta charset="utf-8"><title>Schicht bearbeiten</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="p-4">
<div class="container">
  <h1>Schicht bearbeiten</h1>
  @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif
  <form method="POST" action="/admin/shifts/{{ $shift->id }}">@csrf @method('PUT')
    <div class="mb-3"><label class="form-label">Name</label>
      <input class="form-control" name="name" value="{{ $shift->name }}" required></div>
    <div class="mb-3"><label class="form-label">Farbe (optional)</label>
      <input class="form-control form-control-color" type="color" name="color" value="{{ $shift->color ?? '#007bff' }}">
    </div>
    <button class="btn btn-primary">Speichern</button>
    <a href="/admin/shifts" class="btn btn-outline-secondary">Abbrechen</a>
  </form>
</div></body></html>
