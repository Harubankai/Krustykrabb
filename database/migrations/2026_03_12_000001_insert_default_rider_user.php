<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('user')->updateOrInsert(
            ['email' => 'rider@gmail.com'],
            [
                'name' => 'Rider',
                'password' => Hash::make('rider123'),
                'role' => 'rider',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('user')
            ->where('email', 'rider@gmail.com')
            ->delete();
    }
};
