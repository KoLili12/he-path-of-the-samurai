@extends('layouts.app')

@section('content')
<div class="container py-3">
  <h3 class="mb-3">NASA OSDR</h3>
  <div class="small text-muted mb-2">Источник {{ $src }}</div>

  {{-- Search and Filter Controls --}}
  <div class="row g-3 mb-3">
    <div class="col-md-6">
      <form method="GET" action="/osdr" class="d-flex gap-2">
        <input type="hidden" name="limit" value="{{ $limit }}">
        <input
          type="text"
          name="search"
          class="form-control"
          placeholder="Поиск по названию или ID..."
          value="{{ $search ?? '' }}"
        >
        <button type="submit" class="btn btn-primary">Найти</button>
        @if($search ?? false)
          <a href="/osdr?limit={{ $limit }}" class="btn btn-secondary">Сброс</a>
        @endif
      </form>
    </div>
    <div class="col-md-6">
      <div class="btn-group btn-group-sm" role="group">
        <a href="?limit=1&search={{ $search ?? '' }}" class="btn btn-outline-primary {{ ($limit ?? 1) == 1 ? 'active' : '' }}">1 запись</a>
        <a href="?limit=2&search={{ $search ?? '' }}" class="btn btn-outline-primary {{ ($limit ?? 1) == 2 ? 'active' : '' }}">2 записи</a>
        <a href="?limit=5&search={{ $search ?? '' }}" class="btn btn-outline-primary {{ ($limit ?? 1) == 5 ? 'active' : '' }}">5 записей</a>
        <a href="?limit=10&search={{ $search ?? '' }}" class="btn btn-outline-primary {{ ($limit ?? 1) == 10 ? 'active' : '' }}">10 записей</a>
      </div>
    </div>
  </div>

  @if($search ?? false)
    <div class="alert alert-info mb-3">
      🔍 Результаты поиска для: <strong>{{ $search }}</strong>
      (найдено: <strong>{{ count($items) }}</strong> записей)
    </div>
  @endif

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
