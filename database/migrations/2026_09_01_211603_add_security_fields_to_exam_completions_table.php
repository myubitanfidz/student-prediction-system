<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('exam_completions', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->after('exam_id');
            $table->string('session_nonce')->nullable()->after('started_at');
        });
    }

    public function down()
    {
        Schema::table('exam_completions', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'session_nonce']);
        });
    }
};