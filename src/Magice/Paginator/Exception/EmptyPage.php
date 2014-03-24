<?php
namespace Magice\Paginator\Exception;

/**
 * Exception is thrown when a Page is created and is empty/blank
 **/
class EmptyPage extends InvalidPage
{
    protected $message = "The requested page is empty";
}
