<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueSalesOrderInOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $invoiceNumbers = \App\Order::query()
            ->withTrashed()
            ->selectRaw('COUNT(id) as c, invoice_number')
            ->groupBy('invoice_number')
            ->having('c', '>', 1)
            ->get()
        ;

        $orders = \App\Order::query()
            ->withTrashed()
            ->whereIn('invoice_number', $invoiceNumbers->pluck('invoice_number')->toArray())
            ->orderBy('invoice_number')
            ->get()
        ;

        $lastInvoiceNumber = null;
        $c = 0;
        foreach ($orders as $order) {

            $invoiceNumber = $order->invoice_number;

            if ($invoiceNumber !== $lastInvoiceNumber) {
                $c = 0;
                $lastInvoiceNumber = $invoiceNumber;
            }

            if ($c > 0) {
                $invoiceNumber = "{$invoiceNumber}({$c})";
            }

            $order->invoice_number = $invoiceNumber;
            $order->save();

            $c++;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->string('invoice_number')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_invoice_number_unique');
        });
    }
}
