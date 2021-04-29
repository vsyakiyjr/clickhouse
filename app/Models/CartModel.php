<?php

namespace App\Models;

use App\Api\Structures\Cart;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;

/**
 * App\Models\CartModel
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $user_id
 * @property array $content
 * @property Cart $cart
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CartModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CartModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CartModel query()
 * @mixin \Eloquent
 */
class CartModel extends Model {
    protected $table = 'carts';

    protected $casts = [
        'content' => 'json'
    ];


    public function user(){
    	return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getCartAttribute(){
    	$cart = new Cart($this->content ?? []);
    	$cart->id = $this->id;

    	return $cart;
    }

    public function setCartAttribute(Cart $val){
    	$this->content = $val->toArray();
    }
}
