<?php

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Created by PhpStorm.
 * User: sviatoslav
 * Date: 23.04.19
 * Time: 16:49
 */

class AdminSeeder extends Seeder
{
    public function run()
    {
        $emails = ['alexeevartsiom@gmail.com','ikeamaniaby@gmail.com'];

        User::query()->where('role_id',1)->delete();

        foreach ($emails as $email){
            $user = new User();
            $data = ['role_id'=>'1','name'=>'admin','phone'=>'phone','email'=>$email, 'password'=>Hash::make('admin'),'active'=>'1'];
            $user->create($data);
        }
    }
}