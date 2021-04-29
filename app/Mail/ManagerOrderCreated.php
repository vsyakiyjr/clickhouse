<?php

namespace App\Mail;

use App\Models\OrderDetail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ManagerOrderCreated extends Mailable {
	use Queueable, SerializesModels;

	/**
	 * @var Order
	 */
	private $order;

	/**
	 * Create a new message instance.
	 *
	 * @param Order         $order
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

		return $this->view('emails.managerOrderCreated', [
			'order' => $this->order,
		]);
	}
}
