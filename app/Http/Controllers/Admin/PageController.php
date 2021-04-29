<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pages;
use Illuminate\Http\Request;
use Validator;
use App\Models\PageBlock;

class PageController extends Controller {
	public function index(Request $request) {
		return Pages::with('blocks')->get();
	}

	/**
	 * Store resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$validator = Validator::make($request->all(), [
			'text' => 'required',
		]);

		if ($validator->fails()) {
			return [
				'success' => false,
				'errors'  => $validator->errors(),
			];
		}

		$page = Pages::find($request->id);

		$page->text = $request->text;
		$page->visible = $request->visible;

		$page->save();

        if (isset($request->blocks)) {
            $exsitingIds = [];
            $blocks = [];
            foreach ($request->blocks as $key => $block) {
                if ($block['id']) {
                    $exsitingIds[] = $block['id'];
                    $blocks[$block['id']] = $block;
                } else {
                    if ($block['title'] && $block['text']) {
                        $exsitingIds[] = PageBlock::create([
                            'title' => $block['title'],
                            'text' => $block['text'],
                            'page_id' => $page->id
                        ])->id;
                    }
                }
            }
            $page->blocks()->whereNotIn('id', $exsitingIds)->delete();
            foreach ($page->blocks->whereIn('id', $exsitingIds) as $block) {
                if (isset($blocks[$block->id])) {
                    $title = $blocks[$block->id]['title'];
                    $text = $blocks[$block->id]['text'];
                    if ($title && $text) {
                        $block->title = $title;
                        $block->text = $text;
                        $block->save();
                    }
                }
            }
        }
		return ['success' => true];
	}
}
