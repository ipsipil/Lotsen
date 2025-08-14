@php
    function cell($bookings, $date, $shiftId) {
        $key = $date->toDateString().'#'.$shiftId;
        if (!isset($bookings[$key])) return '';
        return $bookings[$key]->map(function($b){
            return e($b->user?->name ?? '—');
        })->implode(', ');
    }
@endphp
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
  h2 { margin: 0 0 8px 0; }
  table { border-collapse: collapse; width: 100%; }
  th, td { border: 1px solid #000; padding: 6px; text-align: center; vertical-align: middle; }
  th { background: #eee; }
  .shift-col { width: 90px; text-align: left; font-weight: bold; }
  .small { font-size: 11px; color: #555; }
</style>
</head>
<body>
  <h2>Schülerlotsen – Wochenübersicht ({{ $monday->format('d.m.Y') }} – {{ $sunday->format('d.m.Y') }})</h2>
  <table>
    <thead>
      <tr>
        <th class="shift-col">Schicht</th>
        @foreach($days as $d)
          <th>
            {{ ['Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag','Sonntag'][$d->dayOfWeekIso-1] }}<br>
            <span class="small">{{ $d->format('d.m.') }}</span>
          </th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @foreach($shifts as $shift)
        <tr>
          <td class="shift-col">{{ $shift->name }}</td>
          @foreach($days as $d)
            <td>{{ cell($bookings, $d, $shift->id) }}</td>
          @endforeach
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
