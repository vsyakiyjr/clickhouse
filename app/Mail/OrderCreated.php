<?php

namespace App\Mail;

use App\Models\DeliveranceDays;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCreated extends Mailable {
	use Queueable, SerializesModels;

	/**
	 * @var Order
	 */
	private $order;

	/**
	 * Create a new message instance.
	 *
	 * @param Order  $order
	 */
	public function __construct(Order $order) {
		//
		$this->order = $order;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build() {

		$nearestDeliveryDay       = DeliveranceDays::dateFromCache();
		$nearestDeliveryDayCarbon = Carbon::parse($nearestDeliveryDay);
		$clientDeliveryDayFrom    = $nearestDeliveryDayCarbon->copy()->addDays(1);
		$clientDeliveryDayTo      = $nearestDeliveryDayCarbon->copy()->addDays(3);

		$this->subject = 'Спасибо за ваш заказ!';

		return $this->view('emails.orderCreated', [
			'order'    => $this->order,

			'nearestDeliveryDayCarbon' => $nearestDeliveryDayCarbon,
			'clientDeliveryDayFrom'    => $clientDeliveryDayFrom,
			'clientDeliveryDayTo'      => $clientDeliveryDayTo,
		]);
	}
}
