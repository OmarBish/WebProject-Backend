<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = [

        'name', 'websiteURL','credit','tags','post_url','active','comment','testers','video',

    ];
    public function testCases()
    {
        return $this->hasMany('App\TestCase');
    }
    public function testResults()
    {
        return $this->hasMany('App\TestResult');
    }

    
}

