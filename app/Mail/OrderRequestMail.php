<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderRequestMail extends Mailable {
	use Queueable, SerializesModels;

	private $name;

	private $phone;

	private $email;

	private $address;

	/**
	 * @var null
	 */
	private $file;

	private $list;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct($name, $phone, $email, $address, $list = null, UploadedFile $file = null) {

		$this->name = $name;
		$this->phone = $phone;
		$this->email = $email;
		$this->list = $list;
		$this->address = $address;
		$this->file = $file;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build() {

		$this->subject = 'Ikeamania.by: Запрос на создание заказа';

		if($this->file){
			$this->attach($this->file, [
				'as' => $this->file->getClientOriginalName(),
			]);
		}

		return $this->view('emails.orderRequestMail', [
			'name'    => $this->name,
			'phone'   => $this->phone,
			'email'   => $this->email,
			'list'    => $this->list,
			'address' => $this->address,
		]);
	}
}
