<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Cat
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Cat[] $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Cat[] $childrenCategories
 * @property-read int|null $children_categories_count
 * @property-read mixed $all_parents
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Cat[] $parents
 * @property-read int|null $parents_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cat query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property string $link
 * @property string $ikea_id
 * @property bool $visible
 * @property float $discount
 * @property bool $is_new
 * @property int|null $page_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Cat extends Model
{
    protected $casts =[
        'visible'  => 'boolean',
        'discount' => 'float',
        'is_new'   => 'boolean',
    ];

    public function parents()
    {
        return $this->belongsToMany(Cat::class, 'cats_cat', 'cat_id', 'parent_id');
    }

    public function children()
    {
        return $this->belongsToMany(Cat::class, 'cats_cat', 'parent_id', 'cat_id');
    }

    public function childrenCategories()
    {
        return $this
            ->belongsToMany(Cat::class, 'cats_cat', 'parent_id', 'cat_id')
            ->with('children');
    }

    public function getAllParentsAttribute()
    {
        $result = collect([]);

        $getParents = function($parents) use ( &$result, &$getParents ) {
            foreach ($parents as $parent) {
                $result->push($parent);
                $getParents($parent->parents);
            }
        };

        $getParents($this->parents); // [1,2]
        return $result;

        ////////

        $allParents = collect([]);

        $parents = $this->parents;

        while($parents->count() > 0) {
            foreach($parents as $parent) {
                $allParents->push($parents);
                $parents = $parent->parent;
            }
        }

        return $allParents;

        //
        $parents = collect([]);

        $parent = $this->parent;

        while(!is_null($parent)) {
            $parents->push($parent);
            $parent = $parent->parent;
        }

        return $parents;
    }
}
