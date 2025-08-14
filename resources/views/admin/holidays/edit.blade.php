<h1>Ferien bearbeiten</h1>
<form method="POST" action="{{ url('/admin/holidays/'.$holiday->id).'?key='.request('key') }}">
  @csrf @method('PUT')
  <p><input name="name" value="{{ $holiday->name }}" required></p>
  <p>Von: <input type="date" name="start_date" value="{{ $holiday->start_date }}" required></p>
  <p>Bis: <input type="date" name="end_date" value="{{ $holiday->end_date }}" required></p>
  <button>Aktualisieren</button>
</form>
