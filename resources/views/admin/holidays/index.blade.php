<h1>Ferien</h1>
<a href="{{ url('/admin/holidays/create') }}?key={{ request('key') }}">Neu</a>
@if(session('ok')) <p>{{ session('ok') }}</p> @endif
<table border="1" cellpadding="6">
  <tr><th>Name</th><th>Von</th><th>Bis</th><th>Aktion</th></tr>
  @foreach($holidays as $h)
  <tr>
    <td>{{ $h->name }}</td>
    <td>{{ $h->start_date }}</td>
    <td>{{ $h->end_date }}</td>
    <td>
      <a href="{{ url('/admin/holidays/'.$h->id.'/edit').'?key='.request('key') }}">Bearbeiten</a>
      <form method="POST" action="{{ url('/admin/holidays/'.$h->id).'?key='.request('key') }}" style="display:inline">
        @csrf @method('DELETE')
        <button onclick="return confirm('Löschen?')">Löschen</button>
      </form>
    </td>
  </tr>
  @endforeach
</table>
