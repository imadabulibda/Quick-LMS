<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRatingToCourseStudentTable extends Migration {
    public function up()
    {
        Schema::table('course_student' , function (Blueprint $table) {
            $table->integer('rating')->unsigned()->default(0)->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('course_student' , function (Blueprint $table) {
            $table->dropColumn('rating');
        });
    }
}
