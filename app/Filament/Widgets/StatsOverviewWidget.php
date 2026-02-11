<?php

namespace App\Filament\Widgets;

use App\Models\TrackEvent;
use Filament\Widgets\ChartWidget;

class StatsOverviewWidget extends ChartWidget
{
    protected ?string $heading = 'Site Trafiği';
    protected int|string|array $columnSpan = 'full';

    public ?string $filter = 'daily';

    protected function getFilters(): ?array
    {
        return [
            'daily' => 'Günlük (Son 30 gün)',
            'weekly' => 'Haftalık (Son 12 hafta)',
            'monthly' => 'Aylık (Son 12 ay)',
            'yearly' => 'Yıllık (Son 5 yıl)',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected ?array $options = [
        'scales' => [
            'y' => [
                'position' => 'left',
                'title' => [
                    'display' => true,
                    'text' => 'Ziyaret',
                ],
            ],
            'y1' => [
                'position' => 'right',
                'grid' => [
                    'drawOnChartArea' => false,
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Ortalama Süre (dk)',
                ],
            ],
        ],
    ];

    protected function getData(): array
    {
        $trafficSeries = TrackEvent::trafficSeries(
            period: $this->filter,
            pageKey: 'home'
        );

        $labels = $trafficSeries->pluck('label')->all();
        $totalVisits = $trafficSeries->pluck('totalVisits')->all();
        $averageMinutes = $trafficSeries->pluck('averageMinutes')->all();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Ziyaret',
                    'data' => $totalVisits,

                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.25)',

                    'fill' => true,
                    'tension' => 0.35,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Ortalama Süre (dk)',
                    'data' => $averageMinutes,

                    'borderColor' => '#F97316',
                    'backgroundColor' => 'rgba(249, 115, 22, 0.25)',

                    'fill' => true,
                    'tension' => 0.35,
                    'yAxisID' => 'y1',
                ],
            ],
        ];
    }
}
