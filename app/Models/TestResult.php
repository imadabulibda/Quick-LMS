<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResult extends Model {

    protected $fillable=['test_id','user_id','test_result'];


    public function answers()
    {
        return $this->hasMany(TestsResultsAnswer::class);
    }
}
