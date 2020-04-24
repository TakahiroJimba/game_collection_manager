<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // テーブル名
        $TABLE_NAME = 'users';

        // データを全削除
        // 参照しているテーブルから削除する
        DB::table($TABLE_NAME)->delete();

        $now = Carbon::now();
        $data = [
            [
              'name'          => "テストユーザ",
              'mail_address'  => "takahiro412.c@gmail.com",
              'password'      => Hash::make("password"),
              'created_at'    => $now,
              'updated_at'    => $now,
            ],
        ];
        DB::table($TABLE_NAME)->insert($data);
    }
}
