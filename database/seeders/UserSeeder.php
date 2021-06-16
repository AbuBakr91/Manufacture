<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $collector = Role::where('slug','collector')->first();
        $manager = Role::where('slug', 'manager')->first();
        $manageUsers = Permission::where('slug','manage-users')->first();
        $machineOperator = Role::where('slug', 'machine-operator')->first();
        $shareholder = Role::where('slug', 'shareholder')->first();
        $department1 = Department::where('id', 1)->first();
        $department2 = Department::where('id', 2)->first();
        $department3 = Department::where('id', 3)->first();

        $user1 = new User();
        $user1->firstname = 'Антон';
        $user1->lastname = 'Рогозников';
        $user1->email = 'anton@emfy.com';
        $user1->password = bcrypt('secret');
        $user1->save();
        $user1->roles()->attach($manager);
        $user1->permissions()->attach($manageUsers);

        $user2 = new User();
        $user2->firstname = 'Артем';
        $user2->lastname = 'Устинов';
        $user2->email = 'artem@emfy.com';
        $user2->password = bcrypt('secret');
        $user2->save();
        $user2->department()->attach($department3);
        $user2->roles()->attach($collector);

        $user3 = new User();
        $user3->firstname = 'Борис';
        $user3->lastname = 'Верник';
        $user3->email = 'bordis@emdfy.com';
        $user3->password = bcrypt('secret');
        $user3->save();

        $user3->department()->attach($department1);
        $user3->roles()->attach($machineOperator);


        $user4 = new User();
        $user4->firstname = 'Юрий';
        $user4->lastname = 'Волков';
        $user4->email = 'volkov@emdfy.com';
        $user4->password = bcrypt('secret');
        $user4->save();
        $user4->department()->attach($department2);
        $user4->roles()->attach($shareholder);
    }
}
