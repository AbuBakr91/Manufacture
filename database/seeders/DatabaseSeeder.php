<?php

namespace Database\Seeders;

use App\Models\MaterialForCard;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//         \App\Models\User::factory(10)->create();
//        $this->call(RoleSeeder::class);
//        $this->call(DeportmentSeeder::class);
//        $this->call(PermissionSeeder::class);
//        $this->call(UserSeeder::class);
//        $this->call(TechnicalCardAndCategoriesSeeder::class);
//        $this->call(MaterilasSeeder::class);
//        $this->call(ProductSeeder::class);
//        $this->call(MaterialNameSeeder::class);
        $this->call(UpdateStockSeeder::class);
    }
}
