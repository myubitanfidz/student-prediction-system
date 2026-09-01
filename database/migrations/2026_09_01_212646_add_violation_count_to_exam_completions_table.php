<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('exam_completions', function (Blueprint $table) {
            $table->unsignedInteger('violation_count')->default(0)->after('session_nonce');
        });
    }

    public function down()
    {
        Schema::table('exam_completions', function (Blueprint $table) {
            $table->dropColumn('violation_count');
        });
    }
};