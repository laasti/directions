<?php

namespace Laasti\Directions\Exceptions;

class MethodNotAllowedException extends \RuntimeException
{
    protected $methods;

    public function __construct(array $methods, $message = "", $code = 0, \Exception $previous = null)
    {
        $this->methods = $methods;
        parent::__construct($message, $code, $previous);
    }

    public function getMethods()
    {
        return $this->methods;
    }
}
