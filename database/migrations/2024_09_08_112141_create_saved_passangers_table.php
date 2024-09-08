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
        Schema::create('saved_passangers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('saved_by')->comment('user_id')->nullable();
            $table->string('email')->nullable();
            $table->string('contact')->nullable();
            $table->string('title')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('type')->comment('Adult/Child/Infant')->nullable();
            $table->string('dob')->nullable();
            $table->tinyInteger('document_type')->comment('1=>Passport; 2=>National ID')->nullable();
            $table->string('document_no')->nullable();
            $table->string('document_expire_date')->nullable();
            $table->string('document_issue_country')->nullable();
            $table->string('nationality')->nullable();
            $table->string('frequent_flyer_no')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_passangers');
    }
};
