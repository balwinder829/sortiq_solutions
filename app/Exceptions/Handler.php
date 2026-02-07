<?php

use Spatie\Permission\Exceptions\UnauthorizedException;
use Throwable;

public function render($request, Throwable $exception)
{
    if ($exception instanceof UnauthorizedException) {
        return response()
            ->view('errors.unauthorized', [], 403);
    }

    return parent::render($request, $exception);
}
