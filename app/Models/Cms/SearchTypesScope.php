<?php

namespace App\Models\Cms;
use Illuminate\Database\Eloquent\Builder;

trait SearchTypesScope {
	/**
	 * Scope a query check string on full stop words.
	 *
	 * @return Builder
	 *@var $string
	 *
	 * @var Builder $query
	 */
	public function scopeCheckFull($query, $string){
		return $query->where($this->searchColumn, $string)->where('match_type', '=' ,'full' );
	}


	/**
	 * Scope a query check string on start stop words.
	 *
	 * @return Builder
	 *@var $string
	 *
	 * @var Builder $query
	 */
	public function scopeCheckStart($query, $string) {
		return $query->whereRaw("position({$this->searchColumn} in ?) = 1", [$string])->where('match_type', '=' ,'start' );
	}

	/**
	 * Scope a query check string on end stop words.
	 *
	 * @return Builder
	 *@var $string
	 *
	 * @var Builder $query
	 */
	public function scopeCheckEnd($query, $string) {
		return $query->whereRaw("position({$this->searchColumn} in ?) = length($this->searchColumn) - length(?) + 1", [$string, $string])->where('match_type', '=' ,'end' );
	}

	/**
	 * Scope a query check string on regular expression stop words.
	 *
	 * @return Builder
	 *@var $string
	 *
	 * @var Builder $query
	 */
	public function scopeCheckRegex($query, $string) {
		return $query->whereRaw("? regexp $this->searchColumn", [$string])->where('match_type', '=' ,'regex' );
	}

	/**
	 * Scope a query check string on full and start stop words.
	 *
	 * @return Builder
	 *@var $string
	 *
	 * @var Builder $query
	 */
	public function scopeCheckAll($query, $string) {
//		$string = \DB::connection()->getPdo()->quote($string);

		$query->orWhere(function (Builder $query) use ($string) {
			$this->scopeCheckStart($query, $string);
		})->orWhere(function (Builder $query) use ($string) {
			$this->scopeCheckFull($query, $string);
		})->orWhere(function (Builder $query) use ($string) {
			$this->scopeCheckEnd($query, $string);
		})/*->orWhere(function (Builder $query) use ($string) {
			$this->scopeCheckRegex($query, $string);
		})*/;

		return $query;
	}
}