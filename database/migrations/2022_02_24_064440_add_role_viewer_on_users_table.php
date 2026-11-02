<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddRoleViewerOnUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // MODIFY COLUMN ... ENUM adalah sintaks khusus MySQL/MariaDB.
        // Pada driver lain (mis. SQLite untuk pengujian) kolom level sudah
        // dibuat oleh migration awal, jadi langkah ini dilewati.
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN level ENUM('Administrator', 'Accounting', 'Viewer')");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
