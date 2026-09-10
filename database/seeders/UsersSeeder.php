<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Admin Demo',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'approved' => 1,
                'voucher_approved' => 1,
            ]
        );
        $admin->syncRoles(['admin']);

        $company = User::firstOrCreate(
            ['email' => 'company@demo.com'],
            [
                'name' => 'Empresa Demo',
                'password' => Hash::make('12345678'),
                'role' => 'company',
                'approved' => 1,
                'voucher_approved' => 1,
                'company_name' => 'Empresa Demo',
                'razon_social' => 'Empresa Demo S.A.',
                'ruc' => '1790012345001',
                'phone' => '0999999999',
                'address' => 'Quito',
                'city' => 'Quito',
                'representative_name' => 'Representante Demo',
            ]
        );
        $company->syncRoles(['company']);

        $user = User::firstOrCreate(
            ['email' => 'user@demo.com'],
            [
                'name' => 'Usuario Demo',
                'password' => Hash::make('12345678'),
                'role' => 'user',
                'approved' => 1,
                'voucher_approved' => 1,
                'cedula' => '0102030405',
            ]
        );
        $user->syncRoles(['user']);
    }
}
