<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('forgot_password_new', 255)->nullable()->after('password');
            $table->timestamp('forgot_password_sent_at')->nullable()->after('forgot_password_new');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['forgot_password_new', 'forgot_password_sent_at']);
        });
    }

};
