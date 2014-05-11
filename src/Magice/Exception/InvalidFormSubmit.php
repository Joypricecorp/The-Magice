<?php
namespace Magice\Exception {
    class InvalidFormSubmit extends Exception
    {
        private $statusCode = 400;

        function setStatusCode($code)
        {
            $this->statusCode = $code;
        }

        function getStatusCode()
        {
            return $this->statusCode;
        }
    }
}