<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private $tables = [
        'users',
        'user_avatars'
    ];
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->cleanDatabase();

        $this->call(UsersTableSeeder::class);
    }

    private function cleanDatabase()
    {
        $connection = DB::connection()->getPDO()->getAttribute(PDO::ATTR_DRIVER_NAME);

        // Disable foreign key checking because truncate() will fail
        if ($connection === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        } elseif ($connection === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        } else {
            throw new \Exception('Driver not supported.');
        }

        foreach ($this->tables as $tableName) {
            DB::table($tableName)->truncate();
        }

        // Enable it back
        if ($connection === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');//FOR MYSQL
        } elseif ($connection === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');//FOR SQLITE
        } else {
            throw new \Exception('Driver not supported.');
        }
    }
}
