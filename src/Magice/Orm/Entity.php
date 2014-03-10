<?php
namespace Magice\Orm {

    abstract class Entity implements \ArrayAccess
    {
        const STATE_UNLOCKED = 0;
        const STATE_LOCKED   = 1;

        private $_state = self::STATE_UNLOCKED;

        /**
         * @var array
         */
        static private $_lockedObjects = array();

        /**
         * @var Manager
         */
        private static $entityManager;

        /**
         * @var array
         */
        private $_data = array();

        /**
         * @param Manager $em
         */
        public static function setEntityManager(Manager $em)
        {
            self::$entityManager = $em;
        }

        /**
         * @return Manager
         */
        private static function getEntityManager()
        {
            if (!self::$entityManager === null) {
                throw new \RuntimeException('No Entity Manager was passed to Entity::setEntityManager().');
            }
            return self::$entityManager;
        }

        public function save()
        {
            self::getEntityManager()->persist($this);
        }

        public function delete()
        {
            self::getEntityManager()->remove($this);
        }

        final public function __get($key)
        {
            return $this->get($key);
        }

        final public function __set($key, $value)
        {
            $this->set($key, $value);
        }

        final public function __isset($key)
        {
            return isset($this->_data[$key]);
        }

        public function __unset($name)
        {
            unset($this->_data[$name]);
        }

        final public function offsetExists($key)
        {
            return $this->__isset($key);
        }

        final public function offsetGet($key)
        {
            return $this->get($key);
        }

        final public function offsetSet($key, $value)
        {
            $this->set($key, $value);
        }

        final public function offsetUnset($key)
        {
            $this->__unset($key);
        }

        public function get($key)
        {
            $methodName = 'get' . ucfirst($key);

            return (method_exists($this, $methodName)) ? $this->$methodName() : $this->_get($key);
        }

        final protected function _get($key)
        {
            if (!isset($this->_data[$key])) {
                return null;
            }
            return $this->_data[$key];
        }

        public function set($key, $value)
        {
            $methodName = 'set' . ucfirst($key);

            if (\method_exists($this, $methodName)) {
                $this->$methodName($value);
            } else {
                $this->_set($key, $value);
            }
        }

        protected function _set($key, $value)
        {
            $this->_data[$key] = $value;
        }

        public function fromArray(array $array, $obj = null)
        {
            if ($obj === null) {
                $obj = $this;
            }

            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $this->fromArray($value, $obj->$key);
                } else {
                    $obj->set($key, $value);
                }
            }
        }

        public function toArray($obj = null)
        {
            if ($obj === null) {
                $obj = $this;
            }

            $array = array();

            if ($obj instanceof self) {
                if ($obj->_state === self::STATE_LOCKED) {
                    return array();
                }

                $originalState = $obj->_state;

                foreach ($this->obtainMetadata()->reflFields as $name => $reflField) {
                    $value = $this->$name;

                    if ($value instanceof self) {
                        $obj->_state = self::STATE_LOCKED;

                        if ($result = $value->toArray()) {
                            $array[$name] = $result;
                        }
                    } else {
                        if ($value instanceof self) {
                            $obj->_state = self::STATE_LOCKED;

                            $array[$name] = $this->toArray($value);
                        } else {
                            $array[$name] = $value;
                        }
                    }
                }

                $obj->_state = $originalState;
            } else {
                if ($obj instanceof \Doctrine\Common\Collections\Collection) {
                    foreach ($obj as $key => $value) {
                        if (in_array(spl_object_hash($obj), self::$_lockedObjects)) {
                            $array[$key] = $obj;
                            continue;
                        }
                        self::$_lockedObjects[] = spl_object_hash($obj);
                        if ($result = $this->toArray($value)) {
                            $array[$key] = $result;
                        }
                    }
                }
            }

            self::$_lockedObjects[] = array();
            return $array;
        }

        public function __toString()
        {
            return var_export($this->obtainIdentifier(), true);
        }

        public function obtainMetadata()
        {
            return self::getEntityManager()->getClassMetadata(get_class($this));
        }

        public function obtainIdentifier()
        {
            return self::getEntityManager()->getUnitOfWork()->getEntityIdentifier($this);
        }

        public function exists()
        {
            $id = $this->obtainIdentifier();

            return (self::getEntityManager()->contains($this) && !empty($id)) ? true : false;
        }

        public function __call($method, $arguments)
        {
            $func      = substr($method, 0, 3);
            $fieldName = substr($method, 3, strlen($method));
            $fieldName = lcfirst($fieldName);

            if ($func == 'get') {
                return $this->$fieldName;
            } else {
                if ($func == 'set') {
                    $this->$fieldName = $arguments[0];
                } else {
                    if ($func == 'has') {
                        return $this->__isset($fieldName);
                    }
                }
            }

            throw new \BadMethodCallException('Method ' . $method . ' does not exist on ActiveEntity ' . get_class($this));
        }

        public static function __callStatic($method, $arguments)
        {
            return call_user_func_array(
                array(self::getEntityManager()->getRepository(get_called_class()), $method),
                $arguments
            );
        }
    }
}