<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        $userId = DB::table('users')->insertGetId(['name'=>'Isaias Santos', 'email'=>'isaikki@gmail.com', 'password'=>Hash::make('123456'), 'created_at'=>$now, 'updated_at'=>$now]);
        DB::table('lt_admin_users')->insert(['id_user'=>$userId, 'created_at'=>$now, 'updated_at'=>$now]);
    }
}
