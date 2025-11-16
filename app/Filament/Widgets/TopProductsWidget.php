<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use App\Models\Product;
use Filament\Widgets\Widget;
use Illuminate\Support\Number;

class TopProductsWidget extends Widget
{
    protected static string $view = 'filament.widgets.top-products-widget';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $topProducts = OrderItem::query()
            ->selectRaw('product_id, SUM(quantity) as total_quantity, SUM(quantity * price) as total_revenue, COUNT(DISTINCT order_id) as total_orders')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['delivered', 'processing', 'shipped'])
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $product = Product::find($item->product_id);
                return [
                    'product_name' => $product ? $product->name : 'Produto Removido',
                    'total_quantity' => $item->total_quantity,
                    'total_orders' => $item->total_orders,
                    'total_revenue' => $item->total_revenue,
                ];
            });

        return [
            'topProducts' => $topProducts,
        ];
    }
}

