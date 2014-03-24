<?php
namespace Magice\Paginator\Exception;

/**
 * Exception is thrown when a Page is created with an invalid page number
 **/
class InvalidPage extends PaginatorException
{
    protected $message = "The requested page is out of range";
}
