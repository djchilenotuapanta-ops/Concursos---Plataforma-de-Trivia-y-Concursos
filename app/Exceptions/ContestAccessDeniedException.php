<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class ContestAccessDeniedException extends Exception
{
    protected $message = 'No tienes permiso para acceder a este concurso.';
    protected $code = Response::HTTP_FORBIDDEN;
}
