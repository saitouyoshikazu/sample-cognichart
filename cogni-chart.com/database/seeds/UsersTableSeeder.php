<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::Table('users')->insert(
            [
                'name'  =>  '',
                'email' =>  '',
                'password'  =>  password_hash("", PASSWORD_BCRYPT),
                'is_super'  =>  true,
                'created_at'    =>  new \DateTime(),
                'updated_at'    =>  new \DateTime()
            ]
        );
    }
}
