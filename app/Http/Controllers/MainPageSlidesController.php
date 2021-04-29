<?php

namespace App\Http\Controllers;

use App\Models\MainPageSlide;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;

class MainPageSlidesController extends Controller {
	public function list(Request $request){
		$type = $request->get('type');
		
		return MainPageSlide::where('type', '=', $type)->orderBy('position')->orderBy('id')->get();
	}

	/**
	 * @param Request $request
	 */
	public function upload(Request $request) {
		/** @var UploadedFile $file */
		$file = $request->file('file');
		$type = $request->get('type');

		$fileName = $file->getClientOriginalName();
		$slidePath = "/slides/$type/$fileName";

		$file->storeAs("slides/$type", $fileName);

		MainPageSlide::create([
			'type'       => $type,
			'slide_path' => $slidePath,
		]);

		return ['success' => true];
	}

	/**
	 * @param Request $request
	 *
	 * @return array
	 */
	public function delete(Request $request){
		$id = $request->get('id');
		MainPageSlide::find($id)->delete();

		return ['success' => true];
	}

	/**
	 * @param Request $request
	 *
	 * @return array
	 */
	public function move(Request $request){
		$id = $request->get('id');
		$pairedId = $request->get('paired_id');

		$slide = MainPageSlide::find($id);
		$pairedSlide = MainPageSlide::find($pairedId);

		$slide->position = $request->get('position');
		$pairedSlide->position = $request->get('paired_position');;

		$slide->save();
		$pairedSlide->save();

		return $slide;
	}

}
