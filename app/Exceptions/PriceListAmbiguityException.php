<?php


namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class PriceListAmbiguityException extends Exception
{
    protected $defaultMessage = 'Multiple matching price lists found with the same criteria and priority.';
    protected $defaultCode = Response::HTTP_CONFLICT;

    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage() ?: $this->defaultMessage,
        ], $this->getCode() ?: $this->defaultCode);
    }
}
