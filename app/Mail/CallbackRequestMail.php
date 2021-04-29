<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CallbackRequestMail extends Mailable {
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
	public function __construct($phone) {

		$this->phone = $phone;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build() {

		$this->subject = 'Ikeamania.by: Запрос на callback';

		return $this->view('emails.callbackRequest', [
			'phone'   => $this->phone,
		]);
	}
}
