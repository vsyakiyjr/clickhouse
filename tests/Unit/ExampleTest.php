<?php

namespace Tests\Unit;

use Tests\TestCase;
use DB;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
    	DB::statement("truncate table charges");
	    DB::table('charges')->insert([
		    ['total_from' => null,   'total_to' => 2500,   'type' => 'fixed',      'amount' => 20],
		    ['total_from' => 2501 ,  'total_to' => 5000,   'type' => 'percentage', 'amount' => 25],
		    ['total_from' => 5001 ,  'total_to' => 10000,  'type' => 'percentage', 'amount' => 17],
		    ['total_from' => 10001,  'total_to' => 20000,  'type' => 'percentage', 'amount' => 13],
		    ['total_from' => 20001,  'total_to' => 40000,  'type' => 'percentage', 'amount' => 10],
		    ['total_from' => 40001,  'total_to' => 100000, 'type' => 'percentage', 'amount' => 8],
		    ['total_from' => 100000, 'total_to' => null,   'type' => 'percentage', 'amount' => 7],
	    ]);
    }
}
