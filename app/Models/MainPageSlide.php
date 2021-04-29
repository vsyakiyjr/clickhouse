<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MainPageSlide
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $slide_path
 * @property string $type
 * @property int $position
 * @property-read mixed $name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MainPageSlide newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MainPageSlide newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MainPageSlide query()
 * @mixin \Eloquent
 */
class MainPageSlide extends Model {
	protected $table = 'main_page_slides';

	protected $fillable = [
		'slide_path',
		'type',
		'position',
	];

	protected $casts = [
		'position' => 'int'
	];

	protected $appends = ['name'];

	public function getNameAttribute(){
		return last(explode('/', $this->slide_path));
	}
}
