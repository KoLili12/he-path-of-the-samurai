<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Space Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <style>#map{height:340px}</style>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  {{-- Modern animations and styling --}}
  @include('layouts.animations')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3 shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="/dashboard">
      <span class="text-primary">Space</span> Dashboard
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link {{ request()->is('dashboard') || request()->is('/') ? 'active' : '' }}" href="/dashboard">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->is('iss') ? 'active' : '' }}" href="/iss">ISS Tracker</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->is('osdr') ? 'active' : '' }}" href="/osdr">NASA OSDR</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->is('telemetry') ? 'active' : '' }}" href="/telemetry">Telemetry</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
@yield('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
