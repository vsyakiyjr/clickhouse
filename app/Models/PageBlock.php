<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PageBlock
 *
 * @property int         $id
 * @property string|null $title
 * @property string|null $text
 * @property int|null    $page_id
 * @property-read mixed  $errors
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PageBlock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PageBlock newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PageBlock query()
 * @mixin \Eloquent
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class PageBlock extends Model
{
    public $timestamps = false;

    protected $table = 'page_blocks';

    protected $fillable = ['title', 'text', 'page_id'];

    protected $appends = ['errors'];

    public function getErrorsAttribute($value) {
        return false;
    }
}
