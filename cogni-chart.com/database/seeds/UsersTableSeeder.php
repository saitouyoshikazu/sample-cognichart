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
                'name'  =>  'CogniChartAdmin',
                'email' =>  'saitoukameidev@gmail.com',
                'password'  =>  password_hash("cognichart_cde32wsxzaq1", PASSWORD_BCRYPT),
                'is_super'  =>  true,
                'created_at'    =>  new \DateTime(),
                'updated_at'    =>  new \DateTime()
            ]
        );
    }
}
