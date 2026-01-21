<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement(
            "ALTER TABLE users CHANGE role_id role VARCHAR(20) DEFAULT 3 COMMENT '1=Admin,2=Vendor,3=Customer'"
        );
    }

    public function down()
    {
        DB::statement(
            "ALTER TABLE users CHANGE role role_id TINYINT DEFAULT 3 COMMENT '1=Admin,2=Vendor,3=Customer'"
        );
    }
};
