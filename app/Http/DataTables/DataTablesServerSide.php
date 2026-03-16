<?php

namespace App\Http\DataTables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Reusable server-side DataTables response.
 * Use in controllers: return DataTablesServerSide::response($request, $query, $config, $rowCallback);
 */
class DataTablesServerSide
{
    /**
     * @param  Request  $request
     * @param  Builder  $query  Base query (will be cloned for count/filtered/data)
     * @param  array  $config  ['orderable' => ['id','name',...], 'searchable' => ['name','email'] or callable]
     * @param  callable  $rowCallback  function($row, $index, $start): array  — return array of cell values for one row
     * @return \Illuminate\Http\JsonResponse
     */
    public static function response(Request $request, Builder $query, array $config, callable $rowCallback)
    {
        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 50);
        if ($length < 1 || $length > 100) {
            $length = 50;
        }

        $total = (clone $query)->count();

        $search = $request->input('search.value');
        if (Str::length(trim($search ?? '')) > 0) {
            $searchable = $config['searchable'] ?? [];
            if (is_callable($searchable)) {
                $searchable($query, $search);
            } else {
                $query->where(function ($q) use ($searchable, $search) {
                    foreach ($searchable as $field) {
                        if (Str::contains($field, '.')) {
                            [$rel, $col] = explode('.', $field, 2);
                            $q->orWhereHas($rel, fn ($sq) => $sq->where($col, 'like', '%' . $search . '%'));
                        } else {
                            $q->orWhere($field, 'like', '%' . $search . '%');
                        }
                    }
                });
            }
        }

        $filteredTotal = $query->count();

        $orderCol = (int) $request->input('order.0.column', 0);
        $orderDir = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $orderable = $config['orderable'] ?? [];
        if (isset($orderable[$orderCol])) {
            $orderField = $orderable[$orderCol];
            if (is_callable($orderField)) {
                $orderField($query, $orderDir);
            } elseif (Str::contains($orderField, '.')) {
                $query->orderBy($orderField, $orderDir);
            } else {
                $query->orderBy($orderField, $orderDir);
            }
        }

        $items = $query->skip($start)->take($length)->get();
        $data = [];
        foreach ($items as $index => $row) {
            $data[] = $rowCallback($row, $index, $start);
        }

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $filteredTotal,
            'data'            => $data,
        ]);
    }
}
