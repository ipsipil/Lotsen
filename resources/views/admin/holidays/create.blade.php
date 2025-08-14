<h1>Ferien anlegen</h1>
<form method="POST" action="{{ url('/admin/holidays').'?key='.request('key') }}">
  @csrf
  <p><input name="name" placeholder="Name" required></p>
  <p>Von: <input type="date" name="start_date" required></p>
  <p>Bis: <input type="date" name="end_date" required></p>
  <button>Speichern</button>
</form>
