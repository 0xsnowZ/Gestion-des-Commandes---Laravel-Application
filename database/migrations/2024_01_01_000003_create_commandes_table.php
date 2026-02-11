<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->dateTime('date');
            $table->enum('statut', [
                'brouillon',
                'en_attente',
                'confirmee',
                'envoyee',
                'livree',
                'retournee',
                'annulee',
                'closee'
            ])->default('brouillon');
            $table->decimal('total_ht', 12, 2)->default(0);
            $table->decimal('tva', 12, 2)->default(0);
            $table->decimal('total_ttc', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('date_validation')->nullable();
            $table->timestamp('date_expedition')->nullable();
            $table->timestamp('date_livraison')->nullable();
            $table->timestamp('date_annulation')->nullable();
            $table->timestamp('date_cloture')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
