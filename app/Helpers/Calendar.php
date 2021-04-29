<?php

namespace App\Helpers;

use App\Models\DeliveranceDays;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;

class Calendar {
	public static function getCalendar($full = false) {
		$deliveryDates = DeliveranceDays::orderBy('date', 'asc')->get(['date'])->map(function ($d) {
			return $d->date;
		})->toArray();

		if($full){
			$dateFrom = Carbon::now()->startOfYear();
			$dateTo   = Carbon::now()->endOfYear()->addMonth();
		} else {
			$dateFrom = Carbon::now()->startOfMonth();
			$dateTo   = Carbon::now()->endOfMonth()->addMonth();
		}

		$period = new DatePeriod(
			$dateFrom,
			new DateInterval('P1D'),
			$dateTo
		);

		$calendar = [];

		foreach ($period as $date) {
			/** @var DateTime $date */
			$year           = $date->format('Y');
			$month          = $date->format('m');
			$weekNumber     = $date->format('W');
			$dayOfWeek      = $date->format('D');
			$day            = $date->format('d');
			$isCurrentMonth = "$year-$month" === date('Y-m');

			$dateFormatted  = $date->format('Y-m-d');
			$isToday        = $dateFormatted == date('Y-m-d');

			$calendar["$year-$month"][$weekNumber][$dayOfWeek] = [
				'date'     => $dateFormatted,
				'day'      => $day,
				'in_range' => $isCurrentMonth,
				'active'   => $isToday,
				'delivery' => in_array($dateFormatted, $deliveryDates),
			];
		}

		return $calendar;
	}

	public static function convertDate($date, $full = false) {
		$month = [
			'Jan' => 'января',
			'Feb' => 'февраля',
			'Mar' => 'марта',
			'Apr' => 'апреля',
			'May' => 'мая',
			'Jun' => 'июня',
			'Jul' => 'июля',
			'Aug' => 'августа',
			'Sep' => 'сентября',
			'Oct' => 'октября',
			'Nov' => 'ноября',
			'Dec' => 'декабря',
		];

		$date = strtotime($date);

		$result = date("d", $date) . " " . $month[date("M", $date)];

		return $full ? $result . " " . date('Y', $date) . 'г.' : $result;
	}

	public static function nextDelivery($format = true, $full = false) {
		static $date;

		if($date && $format){
			return $date;
		}

		$dateModel = DeliveranceDays::whereDate('date', '>', date('Y-m-d'))
		                       ->where('status', true)
		                       ->orderBy('date', 'asc')
		                       ->first();
		if (!$dateModel) {
			return false;
		}

		if($full){
		    return $dateModel;
        }

		$date = $format ? Calendar::convertDate($dateModel->date) : $dateModel->date;

		return $date;
	}
}
