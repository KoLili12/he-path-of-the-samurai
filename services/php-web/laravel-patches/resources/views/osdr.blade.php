@extends('layouts.app')

@section('content')
<div class="container py-3">
  <h3 class="mb-3">NASA OSDR</h3>
  <div class="small text-muted mb-2">Источник {{ $src }}</div>

  {{-- Pagination Controls --}}
  <div class="mb-3">
    <div class="btn-group btn-group-sm" role="group">
      <a href="?limit=1" class="btn btn-outline-primary {{ ($limit ?? 1) == 1 ? 'active' : '' }}">1 запись (~571 датасетов)</a>
      <a href="?limit=2" class="btn btn-outline-primary {{ ($limit ?? 1) == 2 ? 'active' : '' }}">2 записи</a>
      <a href="?limit=5" class="btn btn-outline-primary {{ ($limit ?? 1) == 5 ? 'active' : '' }}">5 записей</a>
      <a href="?limit=10" class="btn btn-outline-primary {{ ($limit ?? 1) == 10 ? 'active' : '' }}">10 записей</a>
    </div>
    <span class="text-muted ms-3 small">⚠️ Каждая запись содержит ~571 датасетов</span>
  </div>

  <div class="table-responsive">
    <table class="table table-sm table-striped align-middle">
      <thead>
        <tr>
          <th>#</th>
          <th>dataset_id</th>
          <th>title</th>
          <th>REST_URL</th>
          <th>updated_at</th>
          <th>inserted_at</th>
          <th>raw</th>
        </tr>
      </thead>
      <tbody>
      @forelse($items as $row)
        <tr>
          <td>{{ $row['id'] }}</td>
          <td>{{ $row['dataset_id'] ?? '—' }}</td>
          <td style="max-width:420px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
            {{ $row['title'] ?? '—' }}
          </td>
          <td>
            @if(!empty($row['rest_url']))
              <a href="{{ $row['rest_url'] }}" target="_blank" rel="noopener">открыть</a>
            @else — @endif
          </td>
          <td>{{ $row['updated_at'] ?? '—' }}</td>
          <td>{{ $row['inserted_at'] ?? '—' }}</td>
          <td>
            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#raw-{{ $row['id'] }}-{{ md5($row['dataset_id'] ?? (string)$row['id']) }}">JSON</button>
          </td>
        </tr>
        <tr class="collapse" id="raw-{{ $row['id'] }}-{{ md5($row['dataset_id'] ?? (string)$row['id']) }}">
          <td colspan="7">
            <pre class="mb-0" style="max-height:260px;overflow:auto">{{ json_encode($row['raw'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</pre>
          </td>
        </tr>
      @empty
        <tr><td colspan="7" class="text-center text-muted">нет данных</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
