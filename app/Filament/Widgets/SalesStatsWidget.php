<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class SalesStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Receita Total (todos os pedidos entregues e processando)
        $totalRevenue = Order::whereIn('status', ['delivered', 'processing', 'shipped'])
            ->sum('total_amount');

        // Receita do Mês Atual
        $monthlyRevenue = Order::whereIn('status', ['delivered', 'processing', 'shipped'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        // Receita do Mês Anterior
        $lastMonthRevenue = Order::whereIn('status', ['delivered', 'processing', 'shipped'])
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total_amount');

        // Variação percentual
        $revenueChange = $lastMonthRevenue > 0 
            ? (($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : 0;

        // Total de Pedidos
        $totalOrders = Order::count();

        // Pedidos do Mês
        $monthlyOrders = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Pedidos do Mês Anterior
        $lastMonthOrders = Order::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $ordersChange = $lastMonthOrders > 0 
            ? (($monthlyOrders - $lastMonthOrders) / $lastMonthOrders) * 100 
            : 0;

        // Ticket Médio
        $averageTicket = $totalOrders > 0 
            ? $totalRevenue / $totalOrders 
            : 0;

        // Ticket Médio do Mês
        $monthlyAverageTicket = $monthlyOrders > 0 
            ? $monthlyRevenue / $monthlyOrders 
            : 0;

        // Ticket Médio do Mês Anterior
        $lastMonthAverageTicket = $lastMonthOrders > 0 
            ? $lastMonthRevenue / $lastMonthOrders 
            : 0;

        $ticketChange = $lastMonthAverageTicket > 0 
            ? (($monthlyAverageTicket - $lastMonthAverageTicket) / $lastMonthAverageTicket) * 100 
            : 0;

        // Pedidos Pendentes
        $pendingOrders = Order::where('status', 'pending')->count();

        return [
            Stat::make('Receita Total', 'R$ ' . Number::format($totalRevenue, locale: 'pt_BR', precision: 2))
                ->description('Todas as vendas confirmadas')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Receita do Mês', 'R$ ' . Number::format($monthlyRevenue, locale: 'pt_BR', precision: 2))
                ->description(
                    $revenueChange >= 0 
                        ? Number::format(abs($revenueChange), precision: 1) . '% aumento vs mês anterior'
                        : Number::format(abs($revenueChange), precision: 1) . '% redução vs mês anterior'
                )
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger'),

            Stat::make('Pedidos do Mês', Number::format($monthlyOrders))
                ->description(
                    $ordersChange >= 0 
                        ? Number::format(abs($ordersChange), precision: 1) . '% aumento vs mês anterior'
                        : Number::format(abs($ordersChange), precision: 1) . '% redução vs mês anterior'
                )
                ->descriptionIcon($ordersChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($ordersChange >= 0 ? 'success' : 'warning'),

            Stat::make('Ticket Médio', 'R$ ' . Number::format($monthlyAverageTicket, locale: 'pt_BR', precision: 2))
                ->description(
                    $ticketChange >= 0 
                        ? Number::format(abs($ticketChange), precision: 1) . '% aumento vs mês anterior'
                        : Number::format(abs($ticketChange), precision: 1) . '% redução vs mês anterior'
                )
                ->descriptionIcon($ticketChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($ticketChange >= 0 ? 'success' : 'warning'),

            Stat::make('Pedidos Pendentes', Number::format($pendingOrders))
                ->description('Aguardando processamento')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}

