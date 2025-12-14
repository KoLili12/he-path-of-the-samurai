<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IssController extends Controller
{
    public function index(Request $request)
    {
        $base = getenv('RUST_BASE') ?: 'http://rust_iss:3000';

        $last  = @file_get_contents($base.'/last');
        $trend = @file_get_contents($base.'/iss/trend');

        $lastJson  = $last  ? json_decode($last,  true) : [];
        $trendJson = $trend ? json_decode($trend, true) : [];

        // Получение истории ISS из БД с поддержкой поиска и сортировки
        $search = $request->query('search', '');
        $limit = (int) $request->query('limit', 20);
        $limit = min(max($limit, 1), 100);

        // Сортировка
        $sortBy = $request->query('sort', 'fetched_at');
        $order = $request->query('order', 'desc');

        // Разрешённые столбцы для сортировки
        $allowedColumns = ['id', 'fetched_at'];
        if (!in_array($sortBy, $allowedColumns)) {
            $sortBy = 'fetched_at';
        }

        // Разрешённые направления
        if (!in_array($order, ['asc', 'desc'])) {
            $order = 'desc';
        }

        $query = DB::table('iss_fetch_log')
            ->select('id', 'fetched_at', 'payload')
            ->orderBy($sortBy, $order);

        if ($search) {
            // Поиск в JSON payload по координатам или другим данным
            $query->whereRaw("payload::text ILIKE ?", ["%{$search}%"]);
        }

        $total = DB::table('iss_fetch_log')->count();
        $history = $query->limit($limit)->get()->map(function($row) {
            $payload = json_decode($row->payload, true);
            return [
                'id' => $row->id,
                'fetched_at' => $row->fetched_at,
                'latitude' => $payload['latitude'] ?? null,
                'longitude' => $payload['longitude'] ?? null,
                'altitude' => $payload['altitude'] ?? null,
                'velocity' => $payload['velocity'] ?? null,
            ];
        });

        return view('iss', [
            'last' => $lastJson,
            'trend' => $trendJson,
            'base' => $base,
            'history' => $history,
            'total' => $total,
            'search' => $search,
            'limit' => $limit,
            'sort' => $sortBy,
            'order' => $order,
        ]);
    }
}
