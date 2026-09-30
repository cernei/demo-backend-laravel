<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('invoice_id', 50)->unique();
            $table->string('customer_id', 50);
            $table->string('customer_name', 255);
            $table->string('customer_email', 255);
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3);
            $table->string('status', 50);
            $table->date('due_date');
            $table->string('comment', 255)->nullable();
            $table->string('shipping_status', 50);
            $table->string('shipping_info', 256)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
