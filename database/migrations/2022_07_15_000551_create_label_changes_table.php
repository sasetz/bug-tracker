<?php

use App\Models\Label;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('label_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Label::class)->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_added');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('label_changes');
    }
};
