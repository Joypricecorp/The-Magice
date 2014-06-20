<?php
/**
 * This file is part of the The Magice Project.
 * @copyright   2012-2014 ツ Joyprice corporation Ltd.
 * @license     http://www.joyprice.org/license
 * @link        http://www.joyprice.org/themagice
 * @author      ツ Liverbool <liverbool@joyprice.com>
 * @version     1.0
 * @since       1.0
 */

namespace Magice\DataType {

    class Integer
    {
        protected $value;

        public function __construct($value)
        {
            if (!is_int($value)) {
                throw new \RuntimeException('Not an integer');
            }

            $this->value = $value;
        }

        public function __toScalar()
        {
            return $this->value;
        }

        public function __fromScalar($value)
        {
            return new static($value);
        }
    }
}
