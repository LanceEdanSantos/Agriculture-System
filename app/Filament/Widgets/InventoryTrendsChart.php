<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\InventoryItem;
use App\Models\ItemRequest;
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

    protected function getContentHeight(): string | int | null
    {
        return '880px'; // You can go higher, e.g., '600px'
    }

    protected function getData(): array
    {
        // Total Stock Levels (12 months)
        $stockLevelData = collect(DB::select("
            SELECT DATE_FORMAT(created_at, '%Y-%m') as date,
                   SUM(current_stock) as total_stock
            FROM inventory_items
            WHERE created_at BETWEEN ? AND ? AND deleted_at IS NULL
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY date ASC
        ", [
            now()->subMonths(11)->startOfMonth()->format('Y-m-d H:i:s'),
            now()->endOfMonth()->format('Y-m-d H:i:s'),
        ]))->map(function ($item) {
            $date = Carbon::createFromFormat('Y-m', $item->date)->endOfMonth();
            return new TrendValue(date: $date, aggregate: (float) $item->total_stock);
        });

        // Fill missing months for stock levels
        $startDate = now()->subMonths(11)->startOfMonth();
        $endDate = now()->endOfMonth();
        $filledStockData = collect();

        while ($startDate->lte($endDate)) {
            $monthKey = $startDate->format('Y-m');

            $existing = $stockLevelData->first(function ($item) use ($monthKey) {
                $itemDate = $item->date instanceof Carbon ? $item->date : Carbon::parse($item->date);
                return $itemDate->format('Y-m') === $monthKey;
            });

            $filledStockData->push($existing ?: new TrendValue(
                date: $startDate->copy()->endOfMonth(),
                aggregate: 0
            ));

            $startDate->addMonth();
        }

        $stockLevelData = $filledStockData;

        // Stock Movements (In vs Out)
        $stockInData = Trend::query(
            StockMovement::where('type', 'in')
                ->whereBetween('created_at', [now()->subMonths(11)->startOfMonth(), now()->endOfMonth()])
        )
        ->between(start: now()->subMonths(11)->startOfMonth(), end: now()->endOfMonth())
        ->perMonth()
        ->sum('quantity');

        $stockOutData = Trend::query(
            StockMovement::where('type', 'out')
                ->whereBetween('created_at', [now()->subMonths(11)->startOfMonth(), now()->endOfMonth()])
        )
        ->between(start: now()->subMonths(11)->startOfMonth(), end: now()->endOfMonth())
        ->perMonth()
        ->sum('quantity');

        // Low Stock Alerts over time
        $lowStockAlerts = collect();
        $currentDate = now()->subMonths(11)->startOfMonth();

        while ($currentDate->lte(now()->endOfMonth())) {
            $monthEnd = $currentDate->copy()->endOfMonth();

            $lowStockCount = InventoryItem::where('created_at', '<=', $monthEnd)
                ->where(function ($query) use ($monthEnd) {
                    $query->whereRaw('current_stock <= minimum_stock')
                          ->where(function ($q) use ($monthEnd) {
                              $q->where('updated_at', '<=', $monthEnd)
                                ->orWhereNull('updated_at');
                          });
                })
                ->count();

            $lowStockAlerts->push(new TrendValue(
                date: $monthEnd,
                aggregate: $lowStockCount
            ));

            $currentDate->addMonth();
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
                    'pointHoverRadius' => 7,
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
                    'pointRadius' => 3,
                    'pointHoverRadius' => 6,
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
                    'pointRadius' => 3,
                    'pointHoverRadius' => 6,
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
                    'pointRadius' => 3,
                    'pointHoverRadius' => 6,
                    'yAxisID' => 'y2',
                ],
            ],
            'labels' => $stockLevelData->map(function (TrendValue $v) {
                $date = $v->date instanceof Carbon ? $v->date : Carbon::parse($v->date);
                return $date->format('M Y');
            }),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'interaction' => ['mode' => 'index', 'intersect' => false],
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                    'ticks' => ['color' => 'rgba(100,100,100,0.8)'],
                ],
                'y' => [
                    'type' => 'linear',
                    'position' => 'left',
                    'title' => ['display' => true, 'text' => 'Stock Levels (Units)'],
                    'ticks' => [
                        'color' => 'rgba(50,50,50,0.8)',
                        'callback' => 'function(value){return value.toLocaleString()}',
                    ],
                    'grid' => ['color' => 'rgba(0,0,0,0.05)'],
                ],
                'y1' => [
                    'type' => 'linear',
                    'position' => 'right',
                    'title' => ['display' => true, 'text' => 'Movements (Units)'],
                    'grid' => ['drawOnChartArea' => false],
                    'ticks' => ['color' => 'rgba(120,120,120,0.8)'],
                ],
                'y2' => [
                    'type' => 'linear',
                    'position' => 'right',
                    'title' => ['display' => true, 'text' => 'Low Stock Alerts'],
                    'grid' => ['drawOnChartArea' => false],
                    'ticks' => ['color' => 'rgba(150,150,150,0.8)'],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'boxWidth' => 20,
                        'color' => 'rgba(60,60,60,0.9)',
                    ],
                ],
                'tooltip' => [
                    'backgroundColor' => 'rgba(17, 24, 39, 0.9)',
                    'titleColor' => '#fff',
                    'bodyColor' => '#fff',
                    'borderWidth' => 1,
                    'borderColor' => 'rgba(255,255,255,0.2)',
                    'padding' => 10,
                    'callbacks' => [
                        'label' => 'function(context) {
                            if (context.dataset.label.includes("Stock Level")) {
                                return context.dataset.label + ": " + context.parsed.y.toLocaleString() + " units";
                            }
                            if (context.dataset.label.includes("Stock In") || context.dataset.label.includes("Stock Out")) {
                                return context.dataset.label + ": " + context.parsed.y.toLocaleString() + " units";
                            }
                            return context.dataset.label + ": " + context.parsed.y;
                        }',
                    ],
                ],
            ],
        ];
    }
}
