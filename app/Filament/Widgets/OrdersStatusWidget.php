<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersStatusWidget extends ChartWidget
{
    protected static ?string $heading = 'Distribuição de Status dos Pedidos';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $statusCounts = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $labels = [];
        $data = [];
        $colors = [];

        $statusLabels = [
            'pending' => ['label' => 'Pendente', 'color' => 'rgb(234, 179, 8)'],
            'processing' => ['label' => 'Processando', 'color' => 'rgb(59, 130, 246)'],
            'shipped' => ['label' => 'Enviado', 'color' => 'rgb(147, 51, 234)'],
            'delivered' => ['label' => 'Entregue', 'color' => 'rgb(34, 197, 94)'],
            'cancelled' => ['label' => 'Cancelado', 'color' => 'rgb(239, 68, 68)'],
        ];

        foreach ($statusLabels as $status => $info) {
            $labels[] = $info['label'];
            $data[] = $statusCounts[$status] ?? 0;
            $colors[] = $info['color'];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pedidos',
                    'data' => $data,
                    'backgroundColor' => array_map(fn($color) => str_replace('rgb', 'rgba', $color) . ', 0.8)', $colors),
                    'borderColor' => $colors,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { return context.label + ": " + context.parsed + " pedidos"; }',
                    ],
                ],
            ],
        ];
    }
}

