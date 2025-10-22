<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class InventoryTrendsChart extends ChartWidget
{
    protected static ?string $heading = 'Inventory Trends Overview';
    protected static ?int $sort = 1;
    protected static string $color = 'primary';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '600px';

    // 👇 Add this property to store the selected filter
    public ?string $filter = '12'; // default: 12 months

    protected function getFilters(): ?array
    {
        return [
            '3' => 'Last 3 Months',
            '6' => 'Last 6 Months',
            '12' => 'Last 12 Months',
        ];
    }

    protected function getData(): array
    {
        // 🧮 Use the selected filter (default 12 months)
        $months = (int) ($this->filter ?? 12);
        $startDate = now()->subMonths($months - 1)->startOfMonth();
        $endDate = now()->endOfMonth();

        // Total Stock Levels
        $stockLevelData = collect(DB::select("
            SELECT DATE_FORMAT(created_at, '%Y-%m') as date,
                   SUM(current_stock) as total_stock
            FROM inventory_items
            WHERE created_at BETWEEN ? AND ? AND deleted_at IS NULL
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY date ASC
        ", [
            $startDate->format('Y-m-d H:i:s'),
            $endDate->format('Y-m-d H:i:s'),
        ]))->map(function ($item) {
            $date = Carbon::createFromFormat('Y-m', $item->date)->endOfMonth();
            return new TrendValue(date: $date, aggregate: (float) $item->total_stock);
        });

        // Fill missing months
        $filledStockData = collect();
        $fillCursor = $startDate->copy();

        while ($fillCursor->lte($endDate)) {
            $monthKey = $fillCursor->format('Y-m');

            $existing = $stockLevelData->first(function ($item) use ($monthKey) {
                $itemDate = $item->date instanceof Carbon ? $item->date : Carbon::parse($item->date);
                return $itemDate->format('Y-m') === $monthKey;
            });

            $filledStockData->push($existing ?: new TrendValue(
                date: $fillCursor->copy()->endOfMonth(),
                aggregate: 0
            ));

            $fillCursor->addMonth();
        }

        $stockLevelData = $filledStockData;

        // Stock Movements
        $stockInData = Trend::query(
            StockMovement::where('type', 'in')
                ->whereBetween('created_at', [$startDate, $endDate])
        )->between(start: $startDate, end: $endDate)
         ->perMonth()
         ->sum('quantity');

        $stockOutData = Trend::query(
            StockMovement::where('type', 'out')
                ->whereBetween('created_at', [$startDate, $endDate])
        )->between(start: $startDate, end: $endDate)
         ->perMonth()
         ->sum('quantity');

        // Low Stock Alerts
        $lowStockAlerts = collect();
        $alertCursor = $startDate->copy();

        while ($alertCursor->lte($endDate)) {
            $monthEnd = $alertCursor->copy()->endOfMonth();

            $lowStockCount = InventoryItem::where('created_at', '<=', $monthEnd)
                ->whereColumn('current_stock', '<=', 'minimum_stock')
                ->count();

            $lowStockAlerts->push(new TrendValue(
                date: $monthEnd,
                aggregate: $lowStockCount
            ));

            $alertCursor->addMonth();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Stock Level',
                    'data' => $stockLevelData->map(fn(TrendValue $v) => round($v->aggregate, 0)),
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.25)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#10B981',
                    'pointRadius' => 4,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Stock In (Monthly)',
                    'data' => $stockInData->map(fn(TrendValue $v) => $v->aggregate),
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.25)',
                    'fill' => false,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#3B82F6',
                    'yAxisID' => 'y1',
                ],
                [
                    'label' => 'Stock Out (Monthly)',
                    'data' => $stockOutData->map(fn(TrendValue $v) => $v->aggregate),
                    'borderColor' => '#EF4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.25)',
                    'fill' => false,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#EF4444',
                    'yAxisID' => 'y1',
                ],
                [
                    'label' => 'Low Stock Alerts',
                    'data' => $lowStockAlerts->map(fn(TrendValue $v) => $v->aggregate),
                    'borderColor' => '#F59E0B',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.25)',
                    'fill' => false,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#F59E0B',
                    'yAxisID' => 'y2',
                ],
            ],
            'labels' => $stockLevelData->map(function (TrendValue $v) {
                $date = $v->date instanceof \Carbon\Carbon ? $v->date : \Carbon\Carbon::parse($v->date);
                return $date->format('M Y');
            }),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
