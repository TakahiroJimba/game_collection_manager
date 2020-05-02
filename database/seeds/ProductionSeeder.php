<?php

use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // マスタ系
        $this->call(AppInfoTableSeeder::class);
        $this->call(LangsTableSeeder::class);
    }
}
