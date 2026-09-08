<?php

namespace App\Services;

use App\Models\User;
use App\Models\Contest;
use App\Notifications\DbNotification;

class NotificationService
{
    public static function send(User $user, string $title, string $message, ?string $url = null, array $extra = []): void
    {
        $data = array_merge([
            'title' => $title,
            'message' => $message,
        ], $extra);

        if ($url) {
            $data['url'] = $url;
        }

        $user->notify(new DbNotification($data));
    }

    public static function profileComplete(User $user): void
    {
        $alreadyNotified = $user->notifications()
            ->where('type', DbNotification::class)
            ->where('data->key', 'complete_profile_birthdate')
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if ($alreadyNotified) {
            return;
        }

        self::send($user, 'Completa tu perfil', 'Registra tu fecha de nacimiento para calcular tu edad y participar en trivias por edad (por ejemplo 18+).', route('profile.edit'), ['key' => 'complete_profile_birthdate']);
    }

    public static function triviaJoined(User $user, Contest $contest): void
    {
        self::send($user, 'Participación confirmada', 'Te has inscrito correctamente en la trivia "' . $contest->title . '".', route('user.contests.list'));
    }

    public static function triviaStarted(User $user, Contest $contest): void
    {
        self::send($user, 'Trivia iniciada', 'Ya estás participando en "' . $contest->title . '". ¡Éxitos!', route('user.trivia.play', $contest));
    }

    public static function triviaCompleted(User $user, Contest $contest, int $correct, int $total, int $wrong, ?int $duration): void
    {
        $message = 'Terminaste "' . $contest->title . '". Aciertos: ' . $correct . '/' . $total . ' | Errores: ' . $wrong;

        if ($duration !== null) {
            $message .= ' | Tiempo: ' . $duration . 's';
        }

        self::send($user, 'Tu resultado está listo', $message, route('user.trivia.result', $contest));
    }

    public static function triviaResultToCompany(User $company, User $participant, Contest $contest, int $correct, int $total, int $wrong, ?int $duration, ?array $leaderData = null): void
    {
        $message = 'Usuario "' . ($participant->name ?: $participant->email) . '" terminó "' . $contest->title . '". Aciertos: ' . $correct . '/' . $total . ' | Errores: ' . $wrong;

        if ($duration !== null) {
            $message .= ' | Tiempo: ' . $duration . 's';
        }

        if ($leaderData) {
            $message .= ' | 1er lugar: ' . $leaderData['name'] . ' (' . $leaderData['correct'] . '/' . $total;

            if (isset($leaderData['duration'])) {
                $message .= ' · ' . $leaderData['duration'] . 's';
            }

            $message .= ')';
        }

        self::send($company, 'Resultado de trivia', $message, route('company.contests.index'));
    }

    public static function contestPublished(User $user, Contest $contest, array $stats): void
    {
        $message = 'Tu concurso "' . $contest->title . '" ha sido publicado. ';
        $message .= 'Tipo: ' . ucfirst($contest->type ?? 'N/A') . ' | ';
        $message .= 'Inicio: ' . ($contest->start_at ? $contest->start_at->format('d/m/Y H:i') : 'N/A') . ' | ';
        $message .= 'Fin: ' . ($contest->end_at ? $contest->end_at->format('d/m/Y H:i') : 'N/A');

        if (!empty($stats['questions'])) {
            $message .= ' | Preguntas: ' . $stats['questions'];
        }

        if (!empty($stats['prizes'])) {
            $message .= ' | Premios: ' . $stats['prizes'];
        }

        self::send($user, 'Concurso publicado', $message, route('company.contests.index'));
    }

    public static function contestUnpublished(User $user, Contest $contest): void
    {
        self::send($user, 'Concurso despublicado', 'Tu concurso "' . $contest->title . '" ha sido despublicado y ya no es visible para los usuarios.', route('company.contests.index'));
    }

    public static function contestDeleted(User $user, string $contestTitle): void
    {
        self::send($user, 'Concurso eliminado', 'El concurso "' . $contestTitle . '" ha sido eliminado permanentemente.', route('company.contests.index'));
    }

    public static function companyApproved(User $user): void
    {
        self::send($user, 'Cuenta aprobada', '¡Tu cuenta de empresa ha sido aprobada! Ya puedes crear concursos.', route('company.dashboard'));
    }

    public static function companyRejected(User $user): void
    {
        self::send($user, 'Cuenta rechazada', 'Tu solicitud de cuenta de empresa ha sido rechazada. Contacta con soporte para más información.', route('company.profile.edit'));
    }

    public static function companySuspended(User $user): void
    {
        self::send($user, 'Cuenta suspendida', 'Tu cuenta de empresa ha sido suspendida. No podrás crear o gestionar concursos.', route('company.dashboard'));
    }

    public static function companyReactivated(User $user): void
    {
        self::send($user, 'Cuenta reactivada', 'Tu cuenta de empresa ha sido reactivada. Ya puedes crear y gestionar concursos nuevamente.', route('company.dashboard'));
    }

    public static function companyDeactivated(User $user): void
    {
        self::send($user, 'Cuenta desactivada', 'Tu cuenta ha sido desactivada. Contacta con soporte si esto es un error.', null);
    }

    public static function adminNotifyApproval(User $admin, User $company): void
    {
        self::send($admin, 'Empresa aprobada', 'Has aprobado la cuenta de la empresa: ' . ($company->company_name ?: $company->email), route('admin.companies.index'));
    }

    public static function adminNotifyRejection(User $admin, User $company): void
    {
        self::send($admin, 'Empresa rechazada', 'Has rechazado la cuenta de la empresa: ' . ($company->company_name ?: $company->email), route('admin.companies.index'));
    }

    public static function adminNotifySuspension(User $admin, User $company): void
    {
        self::send($admin, 'Empresa suspendida', 'Has suspendido la cuenta de la empresa: ' . ($company->company_name ?: $company->email), route('admin.companies.index'));
    }

    public static function adminNotifyReactivation(User $admin, User $company): void
    {
        self::send($admin, 'Empresa reactivada', 'Has reactivado la cuenta de la empresa: ' . ($company->company_name ?: $company->email), route('admin.companies.index'));
    }

    public static function adminNotifyDeactivation(User $admin, User $targetUser): void
    {
        self::send($admin, 'Usuario desactivado', 'Has desactivado al usuario: ' . ($targetUser->name ?: $targetUser->email), route('admin.users.index'));
    }

    public static function userRegistered(User $admin, User $newUser): void
    {
        self::send($admin, 'Nuevo registro', 'Nuevo usuario registrado: ' . ($newUser->name ?: $newUser->email) . ' | Rol: ' . ($newUser->role ?? 'user'), route('admin.users.index'));
    }

    public static function prizeDelivered(User $winner, string $prizeName, Contest $contest): void
    {
        self::send($winner, '¡Premio entregado!', '¡Felicidades! Has recibido el premio "' . $prizeName . '" del concurso "' . $contest->title . '".', route('user.prizes.index'));
    }

    public static function prizeDeliveryConfirmed(User $company, string $prizeName, string $winnerName): void
    {
        self::send($company, 'Premio entregado', 'Has confirmado la entrega del premio "' . $prizeName . '" al ganador: ' . $winnerName, route('company.prizes.index'));
    }

    public static function participationConfirmed(User $user, Contest $contest): void
    {
        self::send($user, 'Participación confirmada', 'Tu participación en el concurso "' . $contest->title . '" ha sido confirmada.', route('user.contests.list'));
    }

    public static function participationCreated(User $company, string $userName, Contest $contest): void
    {
        self::send($company, 'Nueva participación', 'El usuario "' . $userName . '" se ha inscrito en tu concurso "' . $contest->title . '".', route('company.contests.index'));
    }

    public static function winnerSelected(User $winner, Contest $contest, string $prizeName): void
    {
        self::send($winner, '¡Felicidades, ganaste!', '¡Has ganado el premio "' . $prizeName . '" en el concurso "' . $contest->title . '"!', route('user.contests.list'));
    }

    public static function winnersProcessed(User $company, Contest $contest, int $winnersCount): void
    {
        self::send($company, 'Ganadores seleccionados', 'Se han seleccionado ' . $winnersCount . ' ganador(es) para tu concurso "' . $contest->title . '".', route('company.contests.index'));
    }

    public static function winnersNotified(User $admin, Contest $contest, int $winnersCount): void
    {
        self::send($admin, 'Ganadores notificados', 'Se han notificado ' . $winnersCount . ' ganador(es) del concurso "' . $contest->title . '".', route('admin.contests_adv.index'));
    }

    public static function notifyMultiple(array $users, string $title, string $message, ?string $url = null, array $extra = []): void
    {
        foreach ($users as $user) {
            if ($user instanceof User) {
                self::send($user, $title, $message, $url, $extra);
            }
        }
    }
}
