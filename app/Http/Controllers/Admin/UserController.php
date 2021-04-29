<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use DB;
use Illuminate\Http\Request;

class UserController extends Controller {
	public function index(Request $request) {
		$sort = $request->sortBy ? $request->sortBy : 'id';
		$order = $request->descending ? ($request->descending == 'false' ? 'asc' : 'desc') : 'asc';

		$query = Order
			::where(function ($query) use ($request) {
				$query->where('email', 'like', '%' . $request->filter . '%')
				      ->orWhere('phone', 'like', '%' . $request->filter . '%')
				      ->orWhere('name', 'like', '%' . $request->filter . '%');
			})
			->select('orders.*', DB::raw('MAX(orders.delivery_date) as date'))
			->groupBy('phone')
			->orderBy($sort, $order);

		return ($request->rowsPerPage > 0) ? $query->paginate($request->rowsPerPage) : $query->paginate(100);
		/* return User
			 ::where(function ($query) use ($request) {
				 $query->where('email', 'like', '%'.$request->filter.'%') 
					   ->orWhere('phone', 'like', '%'.$request->filter.'%')
					   ->orWhere('name', 'like', '%'.$request->filter.'%');
			 })
			 ->select('users.*')
			 ->select(DB::raw("(SELECT delivery_date FROM orders WHERE orders.phone = users.phone ORDER BY delivery_date DESC) as delivery_date"))
			 ->select(DB::raw("(SELECT delivery_address FROM orders WHERE orders.phone = users.phone ORDER BY delivery_date DESC) as delivery_address"))
			 ->orderBy($sort, $order)
			 ->paginate($request->rowsPerPage ? $request->rowsPerPage : 10);*/
	}
}
