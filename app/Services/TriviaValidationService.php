<?php

namespace App\Services;

use App\Models\Contest;
use Illuminate\Support\Str;

class TriviaValidationService
{
    public static function validateForPublish(Contest $contest): array
    {
        if (($contest->type ?? null) !== 'trivia') {
            return self::error('⚠️ Solo se pueden publicar trivias.');
        }

        $winnerMethod = strtolower(trim((string)($contest->winner_method ?? '')));
        if (!in_array($winnerMethod, ['ranking', 'lottery'], true)) {
            return self::error('⚠️ Antes de publicar, completa el Paso 2: define el método de ganadores (ranking o sorteo).');
        }

        $questions = $contest->questions()->orderBy('id')->get();
        $questionsCount = (int)$questions->count();

        if ($questionsCount < 1) {
            return self::error('⚠️ Debes cargar al menos 1 pregunta antes de publicar.');
        }

        foreach ($questions as $q) {
            $validationError = self::validateQuestion($q);
            if ($validationError) {
                return $validationError;
            }
        }

        $rule = $contest->rules()->first();
        if (!$rule) {
            return self::error('⚠️ Antes de publicar, completa el Paso 2: configurar reglas y premio.');
        }

        $ruleValidation = self::validateRules($rule, $questionsCount);
        if ($ruleValidation) {
            return $ruleValidation;
        }

        $prizeValidation = self::validatePrizes($contest, $winnerMethod, $rule);
        if ($prizeValidation) {
            return $prizeValidation;
        }

        return self::success();
    }

    private static function validateQuestion($question): ?array
    {
        $questionText = trim((string)($question->question ?? ''));
        $a = trim((string)($question->option_a ?? ''));
        $b = trim((string)($question->option_b ?? ''));
        $c = trim((string)($question->option_c ?? ''));
        $d = trim((string)($question->option_d ?? ''));
        $correct = strtolower(trim((string)($question->correct_option ?? '')));

        if ($correct !== '') {
            $correct = Str::of($correct)->replace(['option_', 'opcion_'], '')->toString();
        }

        $validCorrect = in_array($correct, ['a', 'b', 'c', 'd'], true);
        $aud = $question->audience ?? 'all';
        $validAudience = in_array($aud, ['all', 'adult', 'kid'], true);

        $optionsOk = ($a !== '' && $b !== '' && $c !== '' && $d !== '');
        $correctOptionHasValue = match ($correct) {
            'a' => $a !== '',
            'b' => $b !== '',
            'c' => $c !== '',
            'd' => $d !== '',
            default => false,
        };

        if ($questionText === '' || !$optionsOk || !$validCorrect || !$correctOptionHasValue || !$validAudience) {
            return [
                'ok' => false,
                'message' => '⚠️ Hay preguntas incompletas. Revisa que todas tengan enunciado, 4 opciones y una respuesta correcta válida antes de publicar.',
                'invalid_question_id' => (int)$question->id,
            ];
        }

        return null;
    }

    private static function validateRules($rule, int $questionsCount): ?array
    {
        $secondsPerQuestion = (int)($rule->seconds_per_question ?? 0);
        $attempts = (int)($rule->attempts ?? 0);

        if ($secondsPerQuestion < 1 || $attempts < 1) {
            return self::error('⚠️ Antes de publicar, completa el Paso 2: reglas inválidas (tiempo por pregunta e intentos son obligatorios).');
        }

        if (!empty($rule->trivia_questions_count) && (int)$rule->trivia_questions_count !== $questionsCount) {
            return self::error('⚠️ Las reglas no coinciden con el número actual de preguntas. Entra al Paso 2 y guarda de nuevo las reglas.');
        }

        if (!empty($rule->trivia_total_seconds) && (int)$rule->trivia_total_seconds < 1) {
            return self::error('⚠️ Las reglas de tiempo total no son válidas. Entra al Paso 2 y guarda de nuevo las reglas.');
        }

        return null;
    }

    private static function validatePrizes(Contest $contest, string $winnerMethod, $rule): ?array
    {
        if ($winnerMethod === 'ranking') {
            $hasPrize = $contest->prizes()->where('kind', 'ranking')->where('position', 1)->exists();
            if (!$hasPrize) {
                return self::error('⚠️ Antes de publicar, configura el premio de ranking (Paso 2).');
            }
        } else {
            $winnersCount = (int)($rule->winners_count ?? 0);
            if ($winnersCount < 1) {
                return self::error('⚠️ Antes de publicar, configura cuántos ganadores tendrá el sorteo (Paso 2).');
            }

            $hasPrize = $contest->prizes()->where('kind', 'lottery')->where('position', 1)->exists();
            if (!$hasPrize) {
                return self::error('⚠️ Antes de publicar, configura el premio del sorteo (Paso 2).');
            }

            $countPrizes = (int)$contest->prizes()->where('kind', 'lottery')->count();
            if ($countPrizes > 0 && $countPrizes < $winnersCount) {
                return self::error('⚠️ Los premios del sorteo no coinciden con la cantidad de ganadores. Entra al Paso 2 y guarda de nuevo.');
            }
        }

        if ((bool)($contest->draw_enabled ?? false)) {
            $hasDrawPrize = $contest->prizes()->where('kind', 'draw')->exists();
            if (!$hasDrawPrize) {
                return self::error('⚠️ Activaste el sorteo adicional (Top N) pero falta configurar su premio (Paso 2).');
            }

            if ((int)($contest->draw_top_n ?? 0) < 1) {
                return self::error('⚠️ El Top N del sorteo adicional no es válido. Entra al Paso 2 y guarda de nuevo.');
            }
        }

        return null;
    }

    private static function error(string $message, ?int $invalidQuestionId = null): array
    {
        return [
            'ok' => false,
            'message' => $message,
            'invalid_question_id' => $invalidQuestionId,
        ];
    }

    private static function success(): array
    {
        return ['ok' => true, 'message' => 'OK', 'invalid_question_id' => null];
    }
}
