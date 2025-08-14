<!doctype html><html lang="de"><head>
<meta charset="utf-8"><title>Schichten</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="p-4">
<div class="container">
  <div class="d-flex justify-content-between mb-3">
    <h1>Schichten</h1>
    <a class="btn btn-primary" href="/admin/shifts/create">Neu</a>
  </div>
  @if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif
  <table class="table table-sm align-middle">
    <thead><tr><th>ID</th><th>Name</th><th>Farbe</th><th class="text-end">Aktion</th></tr></thead>
    <tbody>
      @foreach($shifts as $s)
        <tr>
          <td>{{ $s->id }}</td>
          <td>{{ $s->name }}</td>
          <td>
            @if($s->color)
              <span class="badge" style="background:{{ $s->color }}">&nbsp;&nbsp;&nbsp;</span>
              <code>{{ $s->color }}</code>
            @endif
          </td>
          <td class="text-end">
            <a class="btn btn-sm btn-outline-primary" href="/admin/shifts/{{ $s->id }}/edit">Bearbeiten</a>
            <form method="POST" action="/admin/shifts/{{ $s->id }}" class="d-inline" onsubmit="return confirm('Löschen?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">Löschen</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <a href="/admin" class="btn btn-outline-secondary">Zurück</a>
</div></body></html>
