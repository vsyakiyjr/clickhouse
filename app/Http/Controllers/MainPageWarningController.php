<?php

namespace App\Http\Controllers;

use App\Models\MainPageWarning;
use Illuminate\Http\Request;

class MainPageWarningController extends Controller {
    public function get(){
    	return MainPageWarning::find(1);
    }

    public function save(Request $request){
	    $mainPageWarning = MainPageWarning::find(1);

		$mainPageWarning->fill($request->all());

		$mainPageWarning->save();

		return $mainPageWarning;
    }
}
