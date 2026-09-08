<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contest;
use App\Services\ContestWinnerService;

class CloseFinishedContests extends Command
{
    protected $signature = 'contests:close-finished';
    protected $description = 'Cierra concursos finalizados y envía notificaciones';

    public function handle()
    {
        $now = now();

        $contests = Contest::where('type', 'trivia')
            ->where('status', 'active')
            ->whereNotNull('end_at')
            ->where('end_at', '<=', $now)
            ->get();

        if ($contests->isEmpty()) {
            $this->info('No hay concursos para cerrar.');
            return self::SUCCESS;
        }

        $service = app(ContestWinnerService::class);

        $closed = 0;

        foreach ($contests as $contest) {
            $result = $service->close($contest, [
                'winners_count' => 1,
                'eligible_top_n' => 0,
            ]);
            if ($result['ok'] ?? false) {
                $closed++;
            }
        }

        $this->info("Concursos cerrados: {$closed}");
        return self::SUCCESS;
    }
}
