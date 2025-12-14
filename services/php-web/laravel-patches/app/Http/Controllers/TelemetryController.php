<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TelemetryController extends Controller
{
    public function index(Request $request)
    {
        $limit = (int) $request->get('limit', 20);
        $limit = min(max($limit, 1), 100); // от 1 до 100

        // Сортировка
        $sortBy = $request->get('sort', 'recorded_at');
        $order = $request->get('order', 'desc');

        // Разрешённые столбцы для сортировки
        $allowedColumns = ['id', 'recorded_at', 'voltage', 'temp', 'source_file', 'operational', 'status'];
        if (!in_array($sortBy, $allowedColumns)) {
            $sortBy = 'recorded_at';
        }

        // Разрешённые направления
        if (!in_array($order, ['asc', 'desc'])) {
            $order = 'desc';
        }

        $total = DB::table('telemetry_legacy')->count();

        $items = DB::table('telemetry_legacy')
            ->select('id', 'recorded_at', 'voltage', 'temp', 'source_file', 'operational', 'status')
            ->orderBy($sortBy, $order)
            ->limit($limit)
            ->get();

        return view('telemetry', [
            'items' => $items,
            'total' => $total,
            'limit' => $limit,
            'sort' => $sortBy,
            'order' => $order,
        ]);
    }
}
