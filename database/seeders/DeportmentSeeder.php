<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DeportmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dep = new Department;
        $dep->name = 'Автоматический монтаж';
        $dep->save();

        $dep2 = new Department;
        $dep2->name = 'Ручной монтаж';
        $dep2->save();

        $dep3 = new Department;
        $dep3->name = 'Сборка';
        $dep3->save();
    }
}
