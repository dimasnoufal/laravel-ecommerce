<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', array_column(\App\Enums\ReportType::cases(), 'value'));
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('file_path')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['type', 'start_date', 'end_date']);
            $table->index(['generated_by', 'created_at']);
        });

        \Illuminate\Support\Facades\DB::statement('ALTER TABLE reports ADD CONSTRAINT check_reports_dates CHECK (start_date <= end_date)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
