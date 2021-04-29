<?php

namespace App\Models;

use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DeliveranceDays
 *
 * @property int         $id
 * @property string      $date
 * @property int         $status
 * @method static Builder|DeliveranceDays newModelQuery()
 * @method static Builder|DeliveranceDays newQuery()
 * @method static Builder|DeliveranceDays query()
 * @mixin \Eloquent
 * @property Carbon|null $minsk_date_from
 * @property Carbon|null $minsk_date_to
 * @property Carbon|null $country_date_from
 * @property Carbon|null $country_date_to
 */
class DeliveranceDays extends Model {
    public $timestamps = false;

    protected $table = 'deliverance_days';

    protected $fillable = [
        'date',
        'status',
        'minsk_date_from',
        'minsk_date_to',
        'country_date_from',
        'country_date_to',
    ];

    protected $dates = [
        'minsk_date_from'  ,
        'minsk_date_to'    ,
        'country_date_from',
        'country_date_to'  ,
    ];

    protected $casts = [
        'status'            => 'integer',
    ];

    protected $appends = [
        'minsk_date_from_formatted',
        'minsk_date_to_formatted',
        'country_date_from_formatted',
        'country_date_to_formatted',

        'minsk_date_from_edit',
        'minsk_date_to_edit',
        'country_date_from_edit',
        'country_date_to_edit',
    ];

    public function getMinskDateFromFormattedAttribute(){
        return $this->minsk_date_from ? $this->minsk_date_from->format('d.m') : '';
    }
    public function getMinskDateFromEditAttribute(){
        return $this->minsk_date_from ? $this->minsk_date_from->format('d.m.Y') : '';
    }
    public function getMinskDateToFormattedAttribute(){
        return $this->minsk_date_to ? $this->minsk_date_to->format('d.m') : '';
    }
    public function getMinskDateToEditAttribute(){
        return $this->minsk_date_to ? $this->minsk_date_to->format('d.m.Y') : '';
    }
    public function getCountryDateFromFormattedAttribute(){
        return $this->country_date_from ? $this->country_date_from->format('d.m') : '';
    }
    public function getCountryDateFromEditAttribute(){
        return $this->country_date_from ? $this->country_date_from->format('d.m.Y') : '';
    }
    public function getCountryDateToFormattedAttribute(){
        return $this->country_date_to ? $this->country_date_to->format('d.m') : '';
    }
    public function getCountryDateToEditAttribute(){
        return $this->country_date_to ? $this->country_date_to->format('d.m.Y') : '';
    }

    public static function dateFromCache() {
        return Cache::rememberForever('date', function () {
            return static::orderBy('date', 'desc')->first()->date;
        });
    }
}
