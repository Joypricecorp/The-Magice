<?php
namespace Magice\Object;

abstract class NullAbstract implements NullInterface
{
    /**
     * @return bool
     */
    public static function isNullObject()
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
     * @param $name
     * @param $arg
     *
     * @return null
     */
    public function __call($name, $arg)
    {
        return null;
    }
}