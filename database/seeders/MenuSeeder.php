<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          // Membaca data dari file JSON
          $data = json_decode(file_get_contents(database_path('seeders/menuseeder.json')), true);

          // Menyisipkan data ke tabel
          DB::table('menus')->insert($data);
    }
}
