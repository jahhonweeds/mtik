<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_pppoe_accounts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pppoe_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('service')->default('pppoe');
            $table->string('profile')->default('default');
            $table->string('remote_address')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('active')->default(true);
            $table->string('mikrotik_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pppoe_accounts');
    }
};