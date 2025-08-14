<!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <title>Admin Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <div class="container" style="max-width:420px">
    <h1 class="mb-3">Admin Login</h1>

    @if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

    <form method="POST" action="/admin/login">
      @csrf
      <div class="mb-3">
        <label class="form-label">Admin‑Key</label>
        <input type="password" name="key" class="form-control" placeholder="Admin‑Key eingeben" required>
      </div>
      <button class="btn btn-primary w-100">Anmelden</button>
    </form>
  </div>
</body>
</html>
