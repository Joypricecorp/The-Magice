<?php
namespace Magice\Session\Attribute {

    use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;

    /**
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class DotNamespacedAttributeBag extends AttributeBag
    {
        /**
         * Namespace character.
         * @var string
         */
        private $namespaceCharacter;

        /**
         * Constructor.
         *
         * @param string $storageKey         Session storage key.
         * @param string $namespaceCharacter Namespace character to use in keys.
         */
        public function __construct($storageKey = '_sf2_attributes', $namespaceCharacter = '.')
        {
            $this->namespaceCharacter = $namespaceCharacter;
            parent::__construct($storageKey);
        }

        /**
         * {@inheritdoc}
         */
        public function has($name)
        {
            if (array_key_exists($name, $this->attributes)) {
                return true;
            }

            $attributes = (array) $this->resolveAttributePath($name);
            $name       = $this->resolveKey($name);

            if (null === $attributes) {
                return false;
            }

            return array_key_exists($name, $attributes);
        }

        /**
         * {@inheritdoc}
         */
        public function get($name, $default = null)
        {
            if (array_key_exists($name, $this->attributes)) {
                $attributes = $this->attributes;
            } else {
                $attributes = (array) $this->resolveAttributePath($name);
                $name       = $this->resolveKey($name);
            }

            if (null === $attributes) {
                return $default;
            }

            return array_key_exists($name, $attributes) ? $attributes[$name] : $default;
        }

        /**
         * {@inheritdoc}
         */
        public function set($name, $value)
        {
            if (array_key_exists($name, $this->attributes)) {
                $this->attributes[$name] = $value;
            } else {
                $attributes = & $this->resolveAttributePath($name, true);
                $name       = $this->resolveKey($name);

                if (is_object($attributes)) {
                    $attributes->$name = $value;
                } else {
                    $attributes[$name] = $value;
                }
            }
        }

        /**
         * {@inheritdoc}
         */
        public function remove($name)
        {
            $retval = null;

            if (array_key_exists($name, $this->attributes)) {
                $retval     = $this->attributes[$name];
                $attributes = & $this->attributes;
            } else {
                $attributes = & $this->resolveAttributePath($name);
                $name       = $this->resolveKey($name);
            }

            if (is_object($attributes)) {
                if (null !== $attributes && property_exists($attributes, $name)) {
                    $retval = $attributes->$name;
                    unset($attributes->$name);
                }
            } else {
                if (null !== $attributes && array_key_exists($name, $attributes)) {
                    $retval = $attributes[$name];
                    unset($attributes[$name]);
                }
            }

            return $retval;
        }

        /**
         * Resolves a path in attributes property and returns it as a reference.
         * This method allows structured namespacing of session attributes.
         *
         * @param string  $name         Key name
         * @param boolean $writeContext Write context, default false
         *
         * @return array
         */
        protected function &resolveAttributePath($name, $writeContext = false)
        {
            $array = & $this->attributes;
            $name  = (strpos($name, $this->namespaceCharacter) === 0) ? substr($name, 1) : $name;

            // Check if there is anything to do, else return
            if (!$name) {
                return $array;
            }

            $parts = explode($this->namespaceCharacter, $name);

            if (count($parts) < 2) {
                if (!$writeContext) {
                    return $array;
                }

                $array[$parts[0]] = array();

                return $array;
            }

            unset($parts[count($parts) - 1]);

            foreach ($parts as $part) {

                if (is_object($array)) {
                    if (null !== $array && !property_exists($array, $part)) {
                        $array->$part = $writeContext ? array() : null;
                    }

                    $array = & $array->$part;

                } else {
                    if (null !== $array && !array_key_exists($part, $array)) {
                        $array[$part] = $writeContext ? array() : null;
                    }

                    $array = & $array[$part];
                }
            }

            return $array;
        }

        /**
         * Resolves the key from the name.
         * This is the last part in a dot separated string.
         *
         * @param string $name
         *
         * @return string
         */
        protected function resolveKey($name)
        {
            if (false !== $pos = strrpos($name, $this->namespaceCharacter)) {
                $name = substr($name, $pos + 1);
            }

            return $name;
        }
    }
}