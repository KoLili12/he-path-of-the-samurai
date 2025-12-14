@extends('layouts.app')

@section('content')
<div class="container py-3">
  <h3 class="mb-3">Телеметрия Legacy</h3>
  <div class="small text-muted mb-3">
    CSV/XLSX данные генерируемые Python Telemetry сервисом
  </div>

  {{-- Статистика --}}
  <div class="row g-3 mb-3">
    <div class="col-md-3">
      <div class="card">
        <div class="card-body text-center">
          <div class="small text-muted">Всего записей</div>
          <div class="fs-4">{{ number_format($total, 0, '', ' ') }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body text-center">
          <div class="small text-muted">Показано</div>
          <div class="fs-4">{{ count($items) }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body text-center">
          <div class="small text-muted">Operational</div>
          <div class="fs-4 text-success">{{ $items->where('operational', true)->count() }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body text-center">
          <div class="small text-muted">Offline</div>
          <div class="fs-4 text-danger">{{ $items->where('operational', false)->count() }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Pagination Controls --}}
  <div class="mb-3">
    <div class="btn-group btn-group-sm" role="group" aria-label="Pagination">
      <a href="?limit=10" class="btn btn-outline-primary {{ $limit == 10 ? 'active' : '' }}">10 записей</a>
      <a href="?limit=20" class="btn btn-outline-primary {{ $limit == 20 ? 'active' : '' }}">20 записей</a>
      <a href="?limit=50" class="btn btn-outline-primary {{ $limit == 50 ? 'active' : '' }}">50 записей</a>
      <a href="?limit=100" class="btn btn-outline-primary {{ $limit == 100 ? 'active' : '' }}">100 записей</a>
    </div>
  </div>

  {{-- Таблица телеметрии --}}
  <div class="table-responsive">
    <table class="table table-sm table-striped table-hover align-middle">
      <thead class="table-dark">
        <tr>
          @php
            $makeSort = function($column, $label) use ($sort, $order, $limit) {
              $newOrder = ($sort === $column && $order === 'asc') ? 'desc' : 'asc';
              $icon = '';
              if ($sort === $column) {
                $icon = $order === 'asc' ? ' ▲' : ' ▼';
              }
              return '<a href="?sort='.$column.'&order='.$newOrder.'&limit='.$limit.'" class="text-white text-decoration-none">'
                     .$label.$icon.'</a>';
            };
          @endphp
          <th>{!! $makeSort('id', '#') !!}</th>
          <th>{!! $makeSort('recorded_at', 'Дата/Время') !!}</th>
          <th>{!! $makeSort('voltage', 'Напряжение (V)') !!}</th>
          <th>{!! $makeSort('temp', 'Температура (°C)') !!}</th>
          <th>{!! $makeSort('source_file', 'Файл источника') !!}</th>
          <th>{!! $makeSort('status', 'Статус') !!}</th>
          <th>{!! $makeSort('operational', 'Operational') !!}</th>
        </tr>
      </thead>
      <tbody>
      @forelse($items as $row)
        <tr>
          <td>{{ $row->id }}</td>
          <td>
            <span class="font-monospace small">
              {{ \Carbon\Carbon::parse($row->recorded_at)->format('Y-m-d H:i:s') }}
            </span>
          </td>
          <td class="text-end">
            <span class="badge bg-info">{{ number_format($row->voltage, 2) }} V</span>
          </td>
          <td class="text-end">
            <span class="badge {{ $row->temp > 30 ? 'bg-danger' : ($row->temp > 20 ? 'bg-warning' : 'bg-success') }}">
              {{ number_format($row->temp, 2) }}°C
            </span>
          </td>
          <td>
            <span class="font-monospace small text-muted">{{ basename($row->source_file) }}</span>
          </td>
          <td>
            <span class="badge {{ $row->status === 'OK' ? 'bg-success' : 'bg-warning' }}">
              {{ $row->status }}
            </span>
          </td>
          <td class="text-center">
            @if($row->operational)
              <span class="text-success">✓</span>
            @else
              <span class="text-danger">✗</span>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="text-center text-muted">
            Нет данных телеметрии
          </td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>

  {{-- Информация о данных --}}
  <div class="alert alert-info mt-3">
    <strong>ℹ️ О данных:</strong> Эти данные генерируются Python Telemetry сервисом каждые 5 минут.
    CSV и XLSX файлы сохраняются в <code>/data/csv/</code>.
    Типизация: timestamp, boolean, numeric, text. Используется parameterized SQL для безопасности.
  </div>
</div>
@endsection
