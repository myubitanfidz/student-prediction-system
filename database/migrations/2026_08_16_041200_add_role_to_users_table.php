<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Add role / teacher enum / login_count for databases created before the base migration included them. */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'teacher', 'student'])->default('student')->after('password');
            });
        } elseif (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'teacher', 'student') NOT NULL DEFAULT 'student'");
        }

        if (! Schema::hasColumn('users', 'login_count')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedInteger('login_count')->default(0)->after('role');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'login_count')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('login_count');
            });
        }

        if (Schema::hasColumn('users', 'role') && Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'student') NOT NULL DEFAULT 'student'");
        }
    }
};
