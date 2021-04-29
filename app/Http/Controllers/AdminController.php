<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DeliveranceDays;
use App\Models\Order;
use Auth;
use Illuminate\Http\Request;

class AdminController extends Controller {
	public function index() {
		return view('admin.index', ['user' => Auth::user()]);
	}

	public function shedule(Request $request) {
		$now = new \DateTime('now');
		$now_date = $now->format('Y-m-d');
		$shedule = DeliveranceDays::all();
		$nearest = DeliveranceDays:: whereRaw('DATE(`date`)>=DATE(\'' . $now_date . '\')')
		                          ->orderBy('date', 'asc')
		                          ->get()
		                          ->first()->date;
		$nearest_date = new \DateTime($nearest);
		$orders_raw = Order::whereDate('deliverance_day', $nearest);
		$orders_sum = $orders_raw->sum('order_sum');
		$orders_count = $orders_raw->count();

		return view('admin.shedule', [
			'shedule'      => $shedule,
			'orders_sum'   => $orders_sum,
			'orders_count' => $orders_count,
			'nearest'      => $nearest_date->format('j M Y'),
		]);
	}
}
