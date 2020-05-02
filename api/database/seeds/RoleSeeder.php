<?php

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get(base_path("database/data/roles.json"));
        $roles = json_decode($json, true);

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
