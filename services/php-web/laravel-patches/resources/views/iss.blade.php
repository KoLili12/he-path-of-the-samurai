@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h2 class="mb-4">
    <span class="text-primary">ISS</span> Данные и История
  </h2>

  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-primary text-white">
          <h5 class="m-0">📍 Последний снимок</h5>
        </div>
        <div class="card-body">
          @if(!empty($last['payload']))
            <ul class="list-group">
              <li class="list-group-item">Широта {{ $last['payload']['latitude'] ?? '—' }}</li>
              <li class="list-group-item">Долгота {{ $last['payload']['longitude'] ?? '—' }}</li>
              <li class="list-group-item">Высота км {{ $last['payload']['altitude'] ?? '—' }}</li>
              <li class="list-group-item">Скорость км/ч {{ $last['payload']['velocity'] ?? '—' }}</li>
              <li class="list-group-item">Время {{ $last['fetched_at'] ?? '—' }}</li>
            </ul>
          @else
            <div class="text-muted">нет данных</div>
          @endif
        </div>
        <div class="card-footer bg-light">
          <small class="text-muted">API: <code>{{ $base }}/last</code></small>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-success text-white">
          <h5 class="m-0">📊 Тренд движения</h5>
        </div>
        <div class="card-body">
          @if(!empty($trend))
            <ul class="list-group">
              <li class="list-group-item">Движение {{ ($trend['movement'] ?? false) ? 'да' : 'нет' }}</li>
              <li class="list-group-item">Смещение км {{ number_format($trend['delta_km'] ?? 0, 3, '.', ' ') }}</li>
              <li class="list-group-item">Интервал сек {{ $trend['dt_sec'] ?? 0 }}</li>
              <li class="list-group-item">Скорость км/ч {{ $trend['velocity_kmh'] ?? '—' }}</li>
            </ul>
          @else
            <div class="text-muted">нет данных</div>
          @endif
        </div>
        <div class="card-footer bg-light">
          <small class="text-muted">API: <code>{{ $base }}/iss/trend</code></small>
        </div>
      </div>
    </div>
  </div>

  {{-- История МКС --}}
  <div class="card shadow-sm">
    <div class="card-header bg-dark text-white">
      <h5 class="m-0">📜 История позиций МКС</h5>
    </div>
    <div class="card-body">

      {{-- Search and Pagination --}}
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <form method="GET" action="/iss" class="d-flex gap-2">
            <input type="hidden" name="limit" value="{{ $limit }}">
            <input
              type="text"
              name="search"
              class="form-control"
              placeholder="Поиск по координатам, высоте, скорости..."
              value="{{ $search ?? '' }}"
            >
            <button type="submit" class="btn btn-primary">Найти</button>
            @if($search ?? false)
              <a href="/iss?limit={{ $limit }}" class="btn btn-secondary">Сброс</a>
            @endif
          </form>
        </div>
        <div class="col-md-6">
          <div class="btn-group btn-group-sm" role="group">
            <a href="?limit=10&search={{ $search ?? '' }}" class="btn btn-outline-primary {{ $limit == 10 ? 'active' : '' }}">10</a>
            <a href="?limit=20&search={{ $search ?? '' }}" class="btn btn-outline-primary {{ $limit == 20 ? 'active' : '' }}">20</a>
            <a href="?limit=50&search={{ $search ?? '' }}" class="btn btn-outline-primary {{ $limit == 50 ? 'active' : '' }}">50</a>
            <a href="?limit=100&search={{ $search ?? '' }}" class="btn btn-outline-primary {{ $limit == 100 ? 'active' : '' }}">100</a>
          </div>
          <span class="text-muted ms-2 small">Всего записей: {{ number_format($total, 0, '', ' ') }}</span>
        </div>
      </div>

      @if($search ?? false)
        <div class="alert alert-info mb-3">
          🔍 Результаты поиска для: <strong>{{ $search }}</strong>
          (найдено: <strong>{{ count($history) }}</strong> записей)
        </div>
      @endif

      {{-- Таблица истории --}}
      <div class="table-responsive">
        <table class="table table-sm table-striped table-hover align-middle">
          <thead class="table-dark">
            <tr>
              @php
                $makeSort = function($column, $label) use ($sort, $order, $limit, $search) {
                  $newOrder = ($sort === $column && $order === 'asc') ? 'desc' : 'asc';
                  $icon = '';
                  if ($sort === $column) {
                    $icon = $order === 'asc' ? ' ▲' : ' ▼';
                  }
                  return '<a href="?sort='.$column.'&order='.$newOrder.'&limit='.$limit.'&search='.urlencode($search).'" class="text-white text-decoration-none">'
                         .$label.$icon.'</a>';
                };
              @endphp
              <th>{!! $makeSort('id', '#') !!}</th>
              <th>{!! $makeSort('fetched_at', 'Дата/Время') !!}</th>
              <th>Широта</th>
              <th>Долгота</th>
              <th>Высота (км)</th>
              <th>Скорость (км/ч)</th>
            </tr>
          </thead>
          <tbody>
          @forelse($history as $row)
            <tr>
              <td>{{ $row['id'] }}</td>
              <td class="font-monospace small">{{ \Carbon\Carbon::parse($row['fetched_at'])->format('Y-m-d H:i:s') }}</td>
              <td class="text-end">{{ number_format($row['latitude'] ?? 0, 6) }}</td>
              <td class="text-end">{{ number_format($row['longitude'] ?? 0, 6) }}</td>
              <td class="text-end">{{ number_format($row['altitude'] ?? 0, 2) }}</td>
              <td class="text-end">{{ number_format($row['velocity'] ?? 0, 2) }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted">Нет данных</td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
