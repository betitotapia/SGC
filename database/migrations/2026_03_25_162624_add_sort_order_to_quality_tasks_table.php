<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quality_tasks', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->nullable()->after('plan_id');
        });

        $tasksByPlan = DB::table('quality_tasks')
            ->orderBy('plan_id')
            ->orderBy('id')
            ->get();

        $currentPlan = null;
        $counter = 0;

        foreach ($tasksByPlan as $task) {
            if ($currentPlan !== $task->plan_id) {
                $currentPlan = $task->plan_id;
                $counter = 1;
            } else {
                $counter++;
            }

            DB::table('quality_tasks')
                ->where('id', $task->id)
                ->update(['sort_order' => $counter]);
        }
    }

    public function down(): void
    {
        Schema::table('quality_tasks', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
