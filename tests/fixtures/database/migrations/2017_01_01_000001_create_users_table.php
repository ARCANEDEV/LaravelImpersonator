<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Class     CreateUsersTable
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CreateUsersTable extends Migration
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        $this->seedTable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }

    /* -----------------------------------------------------------------
     |  Other Method
     | -----------------------------------------------------------------
     */

    private function seedTable()
    {
        $password = bcrypt('password');
        $date     = Carbon::now()->toDateTimeString();

        DB::table('users')->insert([
            [
                'name'       => 'Admin 1',
                'email'      => 'admin1@example.com',
                'password'   => $password,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'name'       => 'User 1',
                'email'      => 'user1@example.com',
                'password'   => $password,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'name'       => 'User 2',
                'email'      => 'user2@example.com',
                'password'   => $password,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'name'       => 'Admin 2',
                'email'      => 'admin2@example.com',
                'password'   => $password,
                'created_at' => $date,
                'updated_at' => $date,
            ],
        ]);
    }
}
