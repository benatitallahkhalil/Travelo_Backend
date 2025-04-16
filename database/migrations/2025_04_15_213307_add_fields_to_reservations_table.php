<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->date('date_debut')->nullable();
            $table->integer('nbr_jour')->nullable();
            $table->decimal('prix_totale', 10, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('date_debut');
            $table->dropColumn('nbr_jour');
            $table->dropColumn('prix_totale');
        });
    }
};
