<?php
namespace Magice\Object;

abstract class NullAbstract implements NullInterface
{
    /**
     * @return bool
     */
    public function isNullObject()
    {
        return true;
    }

    /**
     * @return static
     */
    public static function create()
    {
        return new static;
    }

    /**
     * @return null
     */
    public function __call()
    {
        return null;
    }
}