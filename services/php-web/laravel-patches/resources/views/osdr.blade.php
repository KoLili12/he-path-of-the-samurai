@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h2 class="mb-2">
    <span class="text-primary">NASA</span> OSDR
  </h2>
  <p class="text-muted mb-4">
    🚀 Open Science Data Repository · API: <code>{{ $src }}</code>
  </p>

  {{-- Search and Filter Controls --}}
  <div class="row g-3 mb-3">
    <div class="col-md-6">
      <form method="GET" action="/osdr" class="d-flex gap-2">
        <input type="hidden" name="limit" value="{{ $limit }}">
        <input type="hidden" name="display_limit" value="{{ $displayLimit }}">
        <input
          type="text"
          name="search"
          class="form-control"
          placeholder="Поиск по названию или ID..."
          value="{{ $search ?? '' }}"
        >
        <button type="submit" class="btn btn-primary">Найти</button>
        @if($search ?? false)
          <a href="/osdr?limit={{ $limit }}&display_limit={{ $displayLimit }}" class="btn btn-secondary">Сброс</a>
        @endif
      </form>
    </div>
    <div class="col-md-6">
      <div class="btn-group btn-group-sm" role="group">
        <a href="?limit=1&display_limit={{ $displayLimit }}&search={{ $search ?? '' }}" class="btn btn-outline-primary {{ ($limit ?? 1) == 1 ? 'active' : '' }}">1 запись</a>
        <a href="?limit=2&display_limit={{ $displayLimit }}&search={{ $search ?? '' }}" class="btn btn-outline-primary {{ ($limit ?? 1) == 2 ? 'active' : '' }}">2 записи</a>
        <a href="?limit=5&display_limit={{ $displayLimit }}&search={{ $search ?? '' }}" class="btn btn-outline-primary {{ ($limit ?? 1) == 5 ? 'active' : '' }}">5 записей</a>
        <a href="?limit=10&display_limit={{ $displayLimit }}&search={{ $search ?? '' }}" class="btn btn-outline-primary {{ ($limit ?? 1) == 10 ? 'active' : '' }}">10 записей</a>
      </div>
    </div>
  </div>

  {{-- Display Limit Controls --}}
  <div class="row g-3 mb-3">
    <div class="col-12">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <div class="d-flex flex-wrap align-items-center gap-2">
          <span class="text-muted small">Показано записей:</span>
          <div class="btn-group btn-group-sm" role="group">
            <a href="?limit={{ $limit }}&display_limit=5&search={{ $search ?? '' }}" class="btn btn-outline-secondary {{ $displayLimit == 5 ? 'active' : '' }}">5</a>
            <a href="?limit={{ $limit }}&display_limit=10&search={{ $search ?? '' }}" class="btn btn-outline-secondary {{ $displayLimit == 10 ? 'active' : '' }}">10</a>
            <a href="?limit={{ $limit }}&display_limit=25&search={{ $search ?? '' }}" class="btn btn-outline-secondary {{ $displayLimit == 25 ? 'active' : '' }}">25</a>
            <a href="?limit={{ $limit }}&display_limit=50&search={{ $search ?? '' }}" class="btn btn-outline-secondary {{ $displayLimit == 50 ? 'active' : '' }}">50</a>
            <a href="?limit={{ $limit }}&display_limit=100&search={{ $search ?? '' }}" class="btn btn-outline-secondary {{ $displayLimit == 100 ? 'active' : '' }}">100</a>
          </div>
        </div>
        <span class="badge bg-info text-wrap">
          Отображается {{ count($items) }} из {{ number_format($total, 0, '', ' ') }} записей
        </span>
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
    <table class="table table-sm table-striped table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Dataset ID</th>
          <th>Название</th>
          <th>REST URL</th>
          <th>Обновлено</th>
          <th>Добавлено</th>
          <th>JSON</th>
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
