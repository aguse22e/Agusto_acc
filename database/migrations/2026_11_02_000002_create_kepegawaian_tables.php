<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateKepegawaianTables extends Migration
{
    /**
     * MODUL BARU — Kepegawaian: data karyawan, master shift, jadwal kerja.
     *
     * Jadwal dan kehadiran disatukan dalam satu tabel jadwal_kerja:
     * setiap karyawan maksimal satu entri per tanggal (unique), dan kolom
     * status bergerak dari Terjadwal menjadi realisasi (Hadir/Izin/Sakit/
     * Cuti/Alpa/Libur) sehingga rekap bulanan untuk keperluan penggajian
     * bisa dihitung langsung dari jadwal. gaji_pokok disimpan di karyawan
     * agar rekap jadwal dapat menjadi dasar jurnal beban gaji (lewat
     * Memorial) tanpa modul payroll terpisah.
     */
    public function up()
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_karyawan', 15)->unique();
            $table->string('nama', 100);
            $table->string('jabatan', 50)->nullable();
            $table->string('departemen', 50)->nullable();
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('email', 100)->nullable()->unique();
            $table->date('tanggal_masuk');
            $table->enum('status_karyawan', ['Tetap', 'Kontrak', 'Harian', 'Magang'])->default('Tetap');
            $table->boolean('aktif')->default(true);
            $table->string('npwp', 25)->nullable();
            $table->string('nama_bank', 50)->nullable();
            $table->string('no_rekening', 30)->nullable();
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('shift', function (Blueprint $table) {
            $table->id();
            $table->string('nama_shift', 30);
            $table->time('jam_masuk');
            $table->time('jam_keluar');
            $table->string('keterangan', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('shift')->insert([
            ['nama_shift' => 'Pagi', 'jam_masuk' => '07:00:00', 'jam_keluar' => '15:00:00', 'keterangan' => 'Shift pagi', 'created_at' => now(), 'updated_at' => now()],
            ['nama_shift' => 'Siang', 'jam_masuk' => '15:00:00', 'jam_keluar' => '23:00:00', 'keterangan' => 'Shift siang', 'created_at' => now(), 'updated_at' => now()],
            ['nama_shift' => 'Malam', 'jam_masuk' => '23:00:00', 'jam_keluar' => '07:00:00', 'keterangan' => 'Shift malam (lintas hari)', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::create('jadwal_kerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan');
            $table->date('tanggal');
            $table->foreignId('shift_id')->nullable()->constrained('shift')->nullOnDelete();
            $table->enum('status', ['Terjadwal', 'Hadir', 'Izin', 'Sakit', 'Cuti', 'Alpa', 'Libur'])->default('Terjadwal');
            $table->string('keterangan', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['karyawan_id', 'tanggal']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('jadwal_kerja');
        Schema::dropIfExists('shift');
        Schema::dropIfExists('karyawan');
    }
}
