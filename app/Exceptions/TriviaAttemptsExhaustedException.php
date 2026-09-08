<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class TriviaAttemptsExhaustedException extends Exception
{
    protected $message = 'Has agotado tus intentos.';
    protected $code = Response::HTTP_FORBIDDEN;
}
