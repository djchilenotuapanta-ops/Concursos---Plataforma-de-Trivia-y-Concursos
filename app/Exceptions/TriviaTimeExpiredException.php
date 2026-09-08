<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class TriviaTimeExpiredException extends Exception
{
    protected $message = 'Se terminó tu tiempo para responder la trivia.';
    protected $code = Response::HTTP_FORBIDDEN;
}
