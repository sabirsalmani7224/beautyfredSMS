<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\User;
use App\Models\UserStock;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Export sales as CSV stream.
     */
    public function exportSalesCsv(Request $request): StreamedResponse
    {
        $user = auth()->user();
        $downlineIds = $user->getAllDownlineIds(includeSelf: true);

        $sales = Sale::whereIn('user_id', $downlineIds)
            ->with(['user', 'shop', 'items.product'])
            ->orderByDesc('sale_date')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="beautyfed_sales_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($sales) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Date', 'Sales Person', 'Shop', 'Sale Type', 'Total Amount', 'Net Amount', 'Payment Mode', 'Status']);

            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->id,
                    $sale->sale_date->format('Y-m-d'),
                    $sale->user?->name ?? '—',
                    $sale->shop?->name ?? '—',
                    $sale->sale_type,
                    $sale->total_amount,
                    $sale->net_amount,
                    $sale->payment_mode,
                    ucfirst($sale->status),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export stock ledger as CSV stream.
     */
    public function exportStockCsv(Request $request): StreamedResponse
    {
        $user = auth()->user();
        $downlineIds = $user->getAllDownlineIds(includeSelf: true);

        $stocks = UserStock::whereIn('user_id', $downlineIds)
            ->with(['user', 'product'])
            ->where('quantity', '>', 0)
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="beautyfed_stock_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($stocks) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['User Name', 'Role', 'Product Code', 'Product Name', 'Quantity In Stock', 'Unit Price', 'Total Stock Value']);

            foreach ($stocks as $stock) {
                $val = $stock->quantity * ($stock->product?->unit_price ?? 0);
                fputcsv($file, [
                    $stock->user?->name ?? '—',
                    $stock->user?->role_label ?? '—',
                    $stock->product?->product_code ?? '—',
                    $stock->product?->name ?? '—',
                    $stock->quantity,
                    $stock->product?->unit_price ?? 0,
                    number_format($val, 2, '.', ''),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
