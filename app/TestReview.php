<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestReview extends Model
{
    protected $fillable = [
        'feedback', 'testerRate'
    ];
    public function test()
    {
        return $this->belongsTo('App\Test');
    }
    public function client()
    {
        return $this->belongsTo('App\Client');
    }
    public function tester()
    {
        return $this->belongsTo('App\Tester');
    }
   
}
