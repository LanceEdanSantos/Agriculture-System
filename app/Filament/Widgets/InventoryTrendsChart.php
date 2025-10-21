<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\InventoryItem;
use App\Models\ItemRequest;
use App\Models\PurchaseHistory;
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
        // Inventory Value (12 months)
        $inventoryValueData = collect(DB::select("
            SELECT DATE_FORMAT(created_at, '%Y-%m') as date,
                   SUM(current_stock * unit_cost) as total_value
            FROM inventory_items
            WHERE created_at BETWEEN ? AND ? AND deleted_at IS NULL
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY date ASC
        ", [
            now()->subMonths(11)->startOfMonth()->format('Y-m-d H:i:s'),
            now()->endOfMonth()->format('Y-m-d H:i:s'),
        ]))->map(function ($item) {
            $date = Carbon::createFromFormat('Y-m', $item->date)->endOfMonth();
            return new TrendValue(date: $date, aggregate: (float) $item->total_value);
        });

        // Fill missing months
        $startDate = now()->subMonths(11)->startOfMonth();
        $endDate = now()->endOfMonth();
        $filledData = collect();

        while ($startDate->lte($endDate)) {
            $monthKey = $startDate->format('Y-m');

            $existing = $inventoryValueData->first(function ($item) use ($monthKey) {
                $itemDate = $item->date instanceof Carbon ? $item->date : Carbon::parse($item->date);
                return $itemDate->format('Y-m') === $monthKey;
            });

            $filledData->push($existing ?: new TrendValue(
                date: $startDate->copy()->endOfMonth(),
                aggregate: 0
            ));

            $startDate->addMonth();
        }

        $inventoryValueData = $filledData;

        // Item Requests trend
        $requestsData = Trend::model(ItemRequest::class)
            ->between(start: now()->subMonths(11)->startOfMonth(), end: now()->endOfMonth())
            ->perMonth()
            ->count();

        // Purchases trend
        $purchasesData = Trend::model(PurchaseHistory::class)
            ->between(start: now()->subMonths(11)->startOfMonth(), end: now()->endOfMonth())
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Inventory Value (₱)',
                    'data' => $inventoryValueData->map(fn(TrendValue $v) => round($v->aggregate / 1000, 2)),
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
                    'label' => 'Item Requests',
                    'data' => $requestsData->map(fn(TrendValue $v) => $v->aggregate),
                    'borderColor' => '#F59E0B',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.25)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#F59E0B',
                    'pointRadius' => 3,
                    'pointHoverRadius' => 6,
                    'yAxisID' => 'y1',
                ],
                [
                    'label' => 'Purchases',
                    'data' => $purchasesData->map(fn(TrendValue $v) => $v->aggregate),
                    'borderColor' => '#8B5CF6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.25)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#8B5CF6',
                    'pointRadius' => 3,
                    'pointHoverRadius' => 6,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $inventoryValueData->map(function (TrendValue $v) {
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
                    'title' => ['display' => true, 'text' => 'Inventory Value (₱K)'],
                    'ticks' => [
                        'color' => 'rgba(50,50,50,0.8)',
                        'callback' => 'function(value){return "₱" + value + "k"}',
                    ],
                    'grid' => ['color' => 'rgba(0,0,0,0.05)'],
                ],
                'y1' => [
                    'type' => 'linear',
                    'position' => 'right',
                    'title' => ['display' => true, 'text' => 'Requests / Purchases'],
                    'grid' => ['drawOnChartArea' => false],
                    'ticks' => ['color' => 'rgba(120,120,120,0.8)'],
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
                            if (context.dataset.label.includes("Value")) {
                                return "₱" + context.parsed.y.toLocaleString() + "k";
                            }
                            return context.dataset.label + ": " + context.parsed.y;
                        }',
                    ],
                ],
            ],
        ];
    }
}
