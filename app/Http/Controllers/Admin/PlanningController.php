<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Calendar;
use App\Http\Controllers\Controller;
use App\Models\DeliveranceDays;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PlanningController extends Controller {
	public function index() {
		$calendar = Calendar::getCalendar(true);

		return [
			'success'  => true,
			'calendar' => $calendar,
		];
	}

	public function delivery() {
		$delivery = DeliveranceDays::orderBy('date', 'desc')->take(10)->get();

		return [
			'success'  => true,
			'delivery' => $delivery,
		];
	}

	public function day($date) {
		$fullDate = Calendar::convertDate($date, true);

		$orders = Order::where('delivery_date', $date);
		$delivery = DeliveranceDays::where('date', $date)->first();

		$info = [
			'fullDate'   => $fullDate,
			'delivery'   => $delivery,
			'orderCount' => $orders->count(),
			'orderTotal' => $orders->sum('total'),
		];

		return [
			'success' => true,
			'info'    => $info,
		];
	}

	public function save(Request $request) {
	    $data = $request->all();
        $data = $data['date']['delivery'];
        $data['status'] = 1;

		$dd = DeliveranceDays::where('id', $data['id'] ?? 0)->first();
		$dd = $dd ?? new DeliveranceDays();

		$dd->fill($data);

		foreach (['minsk_date_from_edit', 'minsk_date_to_edit', 'country_date_from_edit', 'country_date_to_edit',] as $df){
		    if($dateFieldValue = $data[$df] ?? ''){
		        $dateField = str_replace('_edit', '', $df);

		        $dd->{$dateField} = Carbon::createFromFormat('d.m.Y', $dateFieldValue);
            }
        }

		$dd->save();

		return ['success' => true];
	}

	public function status($id, $status) {
		$delivery = DeliveranceDays::find($id);
		$delivery->status = $status;
		$delivery->save();

		return ['success' => true];
	}

	public function cancel($date) {
		DeliveranceDays::where(['date' => $date])->delete();

		return ['success' => true];
	}

	public function move($from, $move) {
		$to = DeliveranceDays::where(['date' => $move])->first();

		DeliveranceDays::where(['date' => $from])->delete();
		if (!$to) {
			DeliveranceDays::create([
				'date'   => $move,
				'status' => true,
			]);
		}
		Order::where('delivery_date', $from)->update(['delivery_date' => $move]);

		return ['success' => true];
	}
}
