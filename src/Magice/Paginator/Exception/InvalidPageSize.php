<?php
namespace Magice\Paginator\Exception;

/**
 * Exception is thrown when a Paginator is created with an invalid page size
 **/
class InvalidPageSize extends PaginatorException
{
    protected $message = "The number of rows to display must be an integer greater than 0";
}
