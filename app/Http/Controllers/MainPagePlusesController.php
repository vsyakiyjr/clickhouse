<?php

namespace App\Http\Controllers;

use App\Models\MainPagePlus;
use Illuminate\Http\Request;

class MainPagePlusesController extends Controller {
	public function list() {

		return MainPagePlus::orderBy('position')->orderBy('id')->get();
	}

	/**
	 * @param Request $request
	 */
	public function save(Request $request) {

		$pluses = $request->get('pluses');

		foreach ($pluses as $plus) {
			if(!$plus['plus']){
				continue;
			}

			$plusModel = MainPagePlus::findOrNew($plus['id'] ?? 0);
			$plusModel->fill($plus);
			$plusModel->save();
		}

		return ['success' => true];
	}

	/**
	 * @param Request $request
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function delete(Request $request) {
		$id = $request->get('id');
		MainPagePlus::find($id)->delete();

		return ['success' => true];
	}

	/**
	 * @param Request $request
	 *
	 * @return array
	 */
	public function move(Request $request) {
		$id = $request->get('id');
		$pairedId = $request->get('paired_id');

		$slide = MainPagePlus::find($id);
		$pairedSlide = MainPagePlus::find($pairedId);

		$slide->position = $request->get('position');
		$pairedSlide->position = $request->get('paired_position');

		$slide->save();
		$pairedSlide->save();

		return $slide;
	}
}
