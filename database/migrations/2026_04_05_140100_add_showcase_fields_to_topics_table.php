<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->string('category')->default('General')->after('description');
            $table->unsignedInteger('capacity')->default(1)->after('category');
            $table->string('semester')->nullable()->after('capacity');
            $table->string('difficulty')->default('intermediate')->after('semester');
            $table->text('expected_outcomes')->nullable()->after('difficulty');
        });
    }

    public function down(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'capacity',
                'semester',
                'difficulty',
                'expected_outcomes',
            ]);
        });
    }
};
