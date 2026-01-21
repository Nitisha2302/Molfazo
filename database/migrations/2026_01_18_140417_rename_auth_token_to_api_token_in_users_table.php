<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement(
            "ALTER TABLE users CHANGE auth_token api_token VARCHAR(255) NULL"
        );
    }

    public function down()
    {
        DB::statement(
            "ALTER TABLE users CHANGE api_token auth_token VARCHAR(255) NULL"
        );
    }
};
