<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LangsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // テーブル名
        $TABLE_NAME = 'langs';

        // データを全削除
        // 参照しているテーブルから削除する
        DB::table($TABLE_NAME)->delete();

        $data = [
            [ 'name' => "日本語", ],
            [ 'name' => "English", ],
        ];
    DB::table($TABLE_NAME)->insert($data);
    }
}
