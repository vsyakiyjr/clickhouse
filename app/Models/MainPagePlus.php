<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MainPagePlus
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MainPagePlus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MainPagePlus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MainPagePlus query()
 * @mixin \Eloquent
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $plus
 * @property int $position
 */
class MainPagePlus extends Model {
    protected $table = 'main_page_pluses';

    protected $fillable = [
        'plus',
        'position'
    ];
}
