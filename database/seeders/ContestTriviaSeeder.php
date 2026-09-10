<?php
/**
 * Archivo: database/seeders/ContestTriviaSeeder.php
 *
 * Seeder: inserta datos iniciales (roles, permisos, etc.).
 *
 * Nota: Comentarios añadidos para que el código sea más entendible (en español).
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Contest;
use App\Models\ContestRule;
use App\Models\ContestQuestion;

class ContestTriviaSeeder extends Seeder
{
    // Método: run() - lógica de este archivo.
    public function run(): void
    {
        $company = User::where('email', 'company@demo.com')->first();

        if (!$company) {
            $this->command->warn('No existe company@demo.com. Ejecuta UsersSeeder primero.');
            return;
        }

        $contest = Contest::firstOrCreate(
            ['title' => 'Trivia Demo'],
            [
                'company_id' => $company->id,
                'type' => 'trivia',
                'description' => 'Concurso de prueba para reglas de trivia',
                'start_at' => now()->subMinutes(10),
                'end_at' => now()->addDays(2),
                'status' => 'active',
            ]
        );

        ContestRule::updateOrCreate(
            ['contest_id' => $contest->id],
            [
                'seconds_per_question' => 10,
                'attempts' => 2,
                'expires_after_join_minutes' => 2,
            ]
        );

        ContestQuestion::updateOrCreate(
            ['contest_id' => $contest->id, 'order' => 1],
            [
                'question' => '¿Capital de Ecuador?',
                'option_a' => 'Quito',
                'option_b' => 'Guayaquil',
                'option_c' => 'Cuenca',
                'option_d' => 'Loja',
                'correct_option' => 'a',
            ]
        );

        ContestQuestion::updateOrCreate(
            ['contest_id' => $contest->id, 'order' => 2],
            [
                'question' => '¿2 + 2 = ?',
                'option_a' => '3',
                'option_b' => '4',
                'option_c' => '5',
                'option_d' => '6',
                'correct_option' => 'b',
            ]
        );

        $this->command->info('Trivia Demo creada correctamente.');
    }
}
