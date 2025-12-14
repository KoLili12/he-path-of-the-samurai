@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h2 class="mb-2">
    <span class="text-primary">Telemetry</span> Legacy
  </h2>
  <p class="text-muted mb-4">
    📊 CSV/XLSX данные генерируемые Python Telemetry сервисом каждые 5 минут
  </p>

  {{-- Статистика --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card shadow-sm border-primary">
        <div class="card-body text-center">
          <div class="text-primary fs-3">📦</div>
          <div class="small text-muted">Всего записей</div>
          <div class="fs-4 fw-bold">{{ number_format($total, 0, '', ' ') }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card shadow-sm border-info">
        <div class="card-body text-center">
          <div class="text-info fs-3">👁️</div>
          <div class="small text-muted">Показано</div>
          <div class="fs-4 fw-bold">{{ count($items) }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card shadow-sm border-success">
        <div class="card-body text-center">
          <div class="text-success fs-3">✅</div>
          <div class="small text-muted">Operational</div>
          <div class="fs-4 fw-bold text-success">{{ $items->where('operational', true)->count() }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card shadow-sm border-danger">
        <div class="card-body text-center">
          <div class="text-danger fs-3">❌</div>
          <div class="small text-muted">Offline</div>
          <div class="fs-4 fw-bold text-danger">{{ $items->where('operational', false)->count() }}</div>
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
  <div class="card mt-4 border-info">
    <div class="card-header bg-info bg-opacity-10 border-info">
      <h6 class="m-0 text-info">ℹ️ Информация о данных</h6>
    </div>
    <div class="card-body">
      <ul class="mb-0">
        <li><strong>Источник:</strong> Python Telemetry Generator (генерация каждые 5 минут)</li>
        <li><strong>Формат:</strong> CSV и XLSX файлы в <code>/data/csv/</code></li>
        <li><strong>Типы данных:</strong> timestamp, boolean, numeric, text</li>
        <li><strong>Безопасность:</strong> Parameterized SQL запросы</li>
      </ul>
    </div>
  </div>
</div>
@endsection
