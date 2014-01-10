<?php
namespace Magice\Utils\Classes {

    /**
     * Class GetterSetter
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */

    /**
     * @method $this setStrict(bool $mode);
     * @method bool getStrict($mode);
     */
    class GetterSetter
    {
        protected $strict = true;

        /**
         * Getter
         *
         * @param string $name
         *
         * @return mixed
         * @throws \RuntimeException
         */
        public function __get($name)
        {
            if ($this->strict && !property_exists($this, $name)) {
                throw new \RuntimeException(sprintf(
                    'Property "%s" undefined on "%s".',
                    $name, get_called_class()
                ));
            }

            return $this->$name;
        }

        /**
         * Setter
         *
         * @param string $name
         * @param mixed  $value
         *
         * @return $this
         * @throws \RuntimeException
         */
        public function __set($name, $value)
        {
            if ($this->strict && !property_exists($this, $name)) {
                throw new \RuntimeException(sprintf(
                    'Cannot set undefined property "%s" on "%s".',
                    $name, get_called_class()
                ));
            }

            $this->$name = $value;

            return $this;
        }

        public function __call($name, $args)
        {
            if (strpos($name, 'set') === 0) {
                $name = lcfirst(substr($name, 3));

                return call_user_func_array(array($this, '__set'), array($name, $args[0]));
            }

            if (strpos($name, 'get') === 0) {
                $name = lcfirst(substr($name, 3));

                return call_user_func_array(array($this, '__get'), array($name));
            }

            return null;
        }
    }
}