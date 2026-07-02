<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class OrdersExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Order::query()->with(['user', 'orderItems.book']);

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        if (!empty($this->filters['customer_id'])) {
            $query->where('user_id', $this->filters['customer_id']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Customer Name',
            'Customer Email',
            'Total Amount',
            'Status',
            'Payment Method',
            'Shipping Address',
            'Created At',
            'Items Count'
        ];
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->user->name,
            $order->user->email,
            number_format($order->total_amount, 2),
            ucfirst($order->status),
            strtoupper($order->payment_method),
            $order->shipping_address,
            $order->created_at->toDateTimeString(),
            $order->orderItems->count()
        ];
    }
}
