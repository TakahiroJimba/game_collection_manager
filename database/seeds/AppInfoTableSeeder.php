<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AppInfoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        require_once app_path() . '/config/const.php';
        // テーブル名
        $TABLE_NAME = 'app_info';

        // Carbonを使ってcreated_atとupdated_atにinsert処理時の時間を入力する
        $now = Carbon::now();

        // データを全削除
        // 参照しているテーブルから削除する
        DB::table($TABLE_NAME)->delete();

        $data = [
            [
                'id'         => GAME_COLLECTION_MGR_APP_ID,
                'name'       => "ゲームコレクションマネージャ",
                'version'    => '0.0.0',
                'custom_url' => '',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => GAME_COLLECTION_APP_ID,
                'name'       => "ゲームコレクション",
                'version'    => '0.0.0',
                'custom_url' => 'gamecollectionapp://',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => REVERSI_APP_ID,
                'name'       => "シンプルリバーシ",
                'version'    => '0.0.0',
                'custom_url' => 'reversiapp://',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        DB::table($TABLE_NAME)->insert($data);
    }
}
