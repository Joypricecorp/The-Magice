<?php
namespace Magice\Object;

abstract class NullAbastract implements NullInterface
{
    public function __call()
    {
        return null;
    }
}