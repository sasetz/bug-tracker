<?php

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
        Schema::create('assignee_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
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
        Schema::dropIfExists('assignee_changes');
    }
};
