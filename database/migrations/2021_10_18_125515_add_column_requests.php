<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->string('requestId')->after('response')->nullable();
            $table->string('expiration')->after('requestId')->nullable();
            $table->string('reference')->after('expiration')->nullable();            
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn('requestId');
            $table->dropColumn('expiration');
            $table->dropColumn('reference');
        });
    }
}
