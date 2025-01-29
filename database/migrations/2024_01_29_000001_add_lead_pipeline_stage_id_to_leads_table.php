<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeadPipelineStageIdToLeadsTable extends Migration
{
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'lead_pipeline_stage_id')) {
                $table->unsignedBigInteger('lead_pipeline_stage_id')->nullable();
                $table->foreign('lead_pipeline_stage_id')
                    ->references('id')
                    ->on('lead_pipeline_stages')
                    ->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['lead_pipeline_stage_id']);
            $table->dropColumn('lead_pipeline_stage_id');
        });
    }
} 