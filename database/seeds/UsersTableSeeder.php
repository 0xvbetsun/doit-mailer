<?php

use App\Models\User;
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
        $this->createAdmin();

//        factory(User::class, 12)->create()->each(function ($user) {
//            $user->role()->save(factory(App\Models\Role::class)->make());
//        });
    }

    private function createAdmin()
    {
        $hasher = app()->make('hash');

        User::create([
            'email' => 'admin@ukr.net',
            'password' => $hasher->make(12301230)
        ]);

    }
}
