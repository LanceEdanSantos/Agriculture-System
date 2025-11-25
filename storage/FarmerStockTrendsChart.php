<?php

namespace App\Filament\Farmer\Widgets;

use App\Models\StockLog;
use App\Models\Item;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FarmerStockTrendsChart extends ChartWidget
{
    protected ?string $heading = 'Your Stock Trends';
    protected string $color = 'primary';
    protected ?string $maxHeight = '500px';
    protected int|string|array $columnSpan = 'full';

    // Default months filter
    public ?string $filter = '12';

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
        $months = (int) ($this->filter ?? 12);
        $startDate = now()->subMonths($months - 1)->startOfMonth();
        $endDate = now()->endOfMonth();

        // Stock In/Out trends
        $stockInTrend = Trend::query(
            StockLog::where('type', 'in')
                ->whereBetween('created_at', [$startDate, $endDate])
        )->between(start: $startDate, end: $endDate)
            ->perMonth()
            ->sum('quantity');

        $stockOutTrend = Trend::query(
            StockLog::where('type', 'out')
                ->whereBetween('created_at', [$startDate, $endDate])
        )->between(start: $startDate, end: $endDate)
            ->perMonth()
            ->sum('quantity');

        // Top 3 items by net movement
        $topItems = StockLog::select(
            'item_id',
            DB::raw('SUM(CASE WHEN type="in" THEN quantity ELSE -quantity END) as net_movement'),
            'items.name as item_name'
        )
            ->join('items', 'stock_logs.item_id', '=', 'items.id')
            ->whereBetween('stock_logs.created_at', [$startDate, $endDate])
            ->groupBy('item_id', 'items.name')
            ->orderByDesc('net_movement')
            ->limit(3)
            ->get();

        $datasets = [
            [
                'label' => 'Stock In',
                'data' => $stockInTrend->map(fn(TrendValue $v) => $v->aggregate),
                'borderColor' => '#10B981',
                'backgroundColor' => 'rgba(16,185,129,0.2)',
                'fill' => true,
                'tension' => 0.4,
            ],
            [
                'label' => 'Stock Out',
                'data' => $stockOutTrend->map(fn(TrendValue $v) => $v->aggregate),
                'borderColor' => '#EF4444',
                'backgroundColor' => 'rgba(239,68,68,0.2)',
                'fill' => true,
                'tension' => 0.4,
            ],
        ];

        // Add top 3 item trends
        $colors = ['#3B82F6', '#8B5CF6', '#F59E0B'];
        foreach ($topItems as $index => $item) {
            $itemTrend = Trend::query(
                StockLog::where('item_id', $item->item_id)
                    ->whereBetween('created_at', [$startDate, $endDate])
            )->between(start: $startDate, end: $endDate)
                ->perMonth()
                ->sum('quantity');

            $datasets[] = [
                'label' => $item->item_name . ' (Net Movement)',
                'data' => $itemTrend->map(fn(TrendValue $v) => $v->aggregate),
                'borderColor' => $colors[$index % count($colors)],
                'borderDash' => [5, 5],
                'borderWidth' => 2,
                'pointRadius' => 3,
            ];
        }

        $labels = $stockInTrend->map(fn(TrendValue $v) => Carbon::parse($v->date)->format('M Y'));

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Quantity (In/Out)',
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
