<?php

use App\Models\Order;
use App\Models\Reservation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable()->after('product_id')->constrained()->onDelete('cascade');
        });

        // Migrate existing reservations to orders
        // Each existing reservation becomes a separate order to maintain uniqueness of existing data structure logic
        // (Since they were made individually)
        $reservations = Reservation::all();
        foreach ($reservations as $reservation) {
            $reference = $reservation->reference ?? '#FESTA-'.strtoupper(Str::random(8));

            $order = Order::create([
                'user_id' => $reservation->user_id,
                'reference' => $reference,
                'created_at' => $reservation->created_at,
                'updated_at' => $reservation->updated_at,
            ]);

            $reservation->order_id = $order->id;
            $reservation->save();
        }

        // Now drop the reference column from reservations as it's on the order
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('reference')->nullable();
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });
    }
};
