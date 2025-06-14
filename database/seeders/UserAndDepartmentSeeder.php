<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;

class UserAndDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // สร้างหน่วยงาน
        $itDept = Department::create(['name' => 'ฝ่ายไอที']);
        $hrDept = Department::create(['name' => 'ฝ่ายบุคคล']);

        // สร้าง admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'department_id' => $itDept->id,
        ]);

        // สร้าง user ปกติ
        User::create([
            'name' => 'ธรรมดา หนึ่ง',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'department_id' => $hrDept->id,
        ]);
    }
}