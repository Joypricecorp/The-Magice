<?php
namespace Magice\Registry {

    use RuntimeException;
    use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
    use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
    use Symfony\Component\DependencyInjection\Exception\ParameterCircularReferenceException;

    /**
     * Class Registry Magice style for Symfony ParameterBag
     * @package     Magice\Registry
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Registry implements ParameterBagInterface, \Iterator, \Countable
    {

        protected $parameters;
        protected $resolved;

        /**
         * Number of elements in configuration data.
         *
         * @var int
         */

        protected $count;
        /**
         * Used when unsetting values during iteration to ensure we do not skip
         * the next element.
         *
         * @var bool
         */
        protected $skipNextIteration;

        /**
         * Constructor.
         *
         * @param array $parameters An array of parameters
         *
         * @api
         */
        public function __construct(array $parameters = array())
        {
            $this->parameters = array();
            $this->add($parameters);
            $this->resolved = false;
        }

        /**
         * Clears all parameters.
         * @api
         */
        public function clear()
        {
            $this->parameters = array();
            $this->count = null;
            $this->skipNextIteration = true;
        }

        /**
         * Adds parameters to the service container parameters.
         *
         * @param array $parameters An array of parameters
         *
         * @api
         */
        public function add(array $parameters)
        {
            foreach ($parameters as $key => $value) {
                $this->parameters[strtolower($key)] = $value;
            }
        }

        /**
         * Gets the service container parameters.
         * @return array An array of parameters
         * @api
         */
        public function all()
        {
            return $this->parameters;
        }

        /**
         * Gets a service container parameter.
         *
         * @param string $name The parameter name
         *
         * @return mixed  The parameter value
         * @throws ParameterNotFoundException if the parameter is not defined
         * @api
         */
        public function get($name)
        {
            $name = strtolower($name);

            if (!array_key_exists($name, $this->parameters)) {
                if (!$name) {
                    throw new ParameterNotFoundException($name);
                }

                $value = $this->_get($name);
                if ($value !== \Magice::VAR_UNDEF) {
                    return $value;
                }

                $alternatives = array();
                foreach (array_keys($this->parameters) as $key) {
                    $lev = levenshtein($name, $key);
                    if ($lev <= strlen($name) / 3 || false !== strpos($key, $name)) {
                        $alternatives[] = $key;
                    }
                }

                throw new ParameterNotFoundException($name, null, null, null, $alternatives);
            }

            return $this->parameters[$name];
        }

        /**
         * Sets a service container parameter.
         *
         * @param string $name  The parameter name
         * @param mixed  $value The parameter value
         *
         * @api
         */
        public function set($name, $value)
        {
            $this->parameters[strtolower($name)] = $value;
        }

        /**
         * Returns true if a parameter name is defined.
         *
         * @param string $name The parameter name
         *
         * @return Boolean true if the parameter name is defined, false otherwise
         * @api
         */
        public function has($name)
        {
            if (array_key_exists(strtolower($name), $this->parameters)) {
                return true;
            }

            return (boolean) ($this->_get($name) !== \Magice::VAR_UNDEF);
        }

        /**
         * Removes a parameter.
         *
         * @param string $name The parameter name
         *
         * @api
         */
        public function remove($name)
        {
            unset($this->parameters[strtolower($name)]);
            $this->count--;
            $this->skipNextIteration = true;
        }

        /**
         * Replaces parameter placeholders (%name%) by their values for all parameters.
         */
        public function resolve()
        {
            if ($this->resolved) {
                return;
            }

            $parameters = array();
            foreach ($this->parameters as $key => $value) {
                try {
                    $value            = $this->resolveValue($value);
                    $parameters[$key] = $this->unescapeValue($value);
                } catch (ParameterNotFoundException $e) {
                    $e->setSourceKey($key);

                    throw $e;
                }
            }

            $this->parameters = $parameters;
            $this->resolved   = true;
        }

        /**
         * Replaces parameter placeholders (%name%) by their values.
         *
         * @param mixed $value     A value
         * @param array $resolving An array of keys that are being resolved (used internally to detect circular references)
         *
         * @return mixed The resolved value
         * @throws ParameterNotFoundException if a placeholder references a parameter that does not exist
         * @throws ParameterCircularReferenceException if a circular reference if detected
         * @throws RuntimeException when a given parameter has a type problem.
         */
        public function resolveValue($value, array $resolving = array())
        {
            if (is_array($value)) {
                $args = array();
                foreach ($value as $k => $v) {
                    $args[$this->resolveValue($k, $resolving)] = $this->resolveValue($v, $resolving);
                }

                return $args;
            }

            if (!is_string($value)) {
                return $value;
            }

            return $this->resolveString($value, $resolving);
        }

        /**
         * Resolves parameters inside a string
         *
         * @param string $value     The string to resolve
         * @param array  $resolving An array of keys that are being resolved (used internally to detect circular references)
         *
         * @return string The resolved string
         * @throws ParameterNotFoundException if a placeholder references a parameter that does not exist
         * @throws ParameterCircularReferenceException if a circular reference if detected
         * @throws RuntimeException when a given parameter has a type problem.
         */
        public function resolveString($value, array $resolving = array())
        {
            // we do this to deal with non string values (Boolean, integer, ...)
            // as the preg_replace_callback throw an exception when trying
            // a non-string in a parameter value
            if (preg_match('/^%([^%\s]+)%$/', $value, $match)) {
                $key = strtolower($match[1]);

                if (isset($resolving[$key])) {
                    throw new ParameterCircularReferenceException(array_keys($resolving));
                }

                $resolving[$key] = true;

                return $this->resolved ? $this->get($key) : $this->resolveValue($this->get($key), $resolving);
            }

            $self = $this;

            return preg_replace_callback(
                '/%%|%([^%\s]+)%/',
                function ($match) use ($self, $resolving, $value) {
                    // skip %%
                    if (!isset($match[1])) {
                        return '%%';
                    }

                    $key = strtolower($match[1]);
                    if (isset($resolving[$key])) {
                        throw new ParameterCircularReferenceException(array_keys($resolving));
                    }

                    $resolved = $self->get($key);

                    if (!is_string($resolved) && !is_numeric($resolved)) {
                        throw new RuntimeException(sprintf(
                            'A string value must be composed of strings and/or numbers, but found parameter "%s" of type %s inside string value "%s".',
                            $key,
                            gettype($resolved),
                            $value
                        ));
                    }

                    $resolved        = (string) $resolved;
                    $resolving[$key] = true;

                    return $self->isResolved() ? $resolved : $self->resolveString($resolved, $resolving);
                },
                $value
            );
        }

        public function isResolved()
        {
            return $this->resolved;
        }

        /**
         * {@inheritDoc}
         */
        public function escapeValue($value)
        {
            if (is_string($value)) {
                return str_replace('%', '%%', $value);
            }

            if (is_array($value)) {
                $result = array();
                foreach ($value as $k => $v) {
                    $result[$k] = $this->escapeValue($v);
                }

                return $result;
            }

            return $value;
        }

        public function unescapeValue($value)
        {
            if (is_string($value)) {
                return str_replace('%%', '%', $value);
            }

            if (is_array($value)) {
                $result = array();
                foreach ($value as $k => $v) {
                    $result[$k] = $this->unescapeValue($v);
                }

                return $result;
            }

            return $value;
        }

        /**
         * Define value with . (dot) nested style
         *
         * @param $path
         * @param $value
         */
        public function def($path, $value)
        {
            $data = $this->parameters;

            // if key exist with . (dot)
            if (isset($data[$path])) {
                $this->set($path, $value);
                return;
            }

            /**
             * Explode the registry path into an array and remove empty
             * nodes that occur as a result of a double dot. ex: joomla..test
             * Finally, re-key the array so they are sequential.
             */
            $nodes = array_values(array_filter(explode('.', $path), 'strlen'));

            if ($nodes) {
                // Initialize the current node to be the registry root.
                $node =& $data;

                // Traverse the registry to find the correct node for the result.
                for ($i = 0, $n = count($nodes) - 1; $i < $n; $i++) {

                    if (!isset($node[$nodes[$i]]) && ($i != $n)) {
                        $node[$nodes[$i]] = array();
                    }

                    $node =& $node[$nodes[$i]];
                }

                // Get the old value if exists so we can return it
                $node[$nodes[$i]] = $value;
            }

            $this->set($path, $value);
        }

        private function _get($path)
        {
            $data = $this->parameters;

            // if key exist with . (dot)
            if (isset($data[$path])) {
                return $data[$path];
            }

            $result = \Magice::VAR_UNDEF;

            // Explode the registry path into an array
            $nodes = explode('.', $path);

            // Initialize the current node to be the registry root.
            $node  = $data;
            $found = false;

            // Traverse the registry to find the correct node for the result.
            foreach ($nodes as $n) {
                if (array_key_exists($n, $node)) {
                    $node  = $node[$n];
                    $found = true;
                } else {
                    $found = false;
                    break;
                }
            }

            if ($found && $node !== $result) {
                $result = $node;
            }


            return $result;
        }

        /**
         * count(): defined by Countable interface.
         *
         * @see    Countable::count()
         * @return int
         */
        public function count()
        {
            return $this->count;
        }

        /**
         * current(): defined by Iterator interface.
         *
         * @see    Iterator::current()
         * @return mixed
         */
        public function current()
        {
            $this->skipNextIteration = false;
            return current($this->parameters);
        }

        /**
         * key(): defined by Iterator interface.
         *
         * @see    Iterator::key()
         * @return mixed
         */
        public function key()
        {
            return key($this->parameters);
        }

        /**
         * next(): defined by Iterator interface.
         *
         * @see    Iterator::next()
         * @return void
         */
        public function next()
        {
            if ($this->skipNextIteration) {
                $this->skipNextIteration = false;
                return;
            }

            next($this->parameters);
        }

        /**
         * rewind(): defined by Iterator interface.
         *
         * @see    Iterator::rewind()
         * @return void
         */
        public function rewind()
        {
            $this->skipNextIteration = false;
            reset($this->parameters);
        }

        /**
         * valid(): defined by Iterator interface.
         *
         * @see    Iterator::valid()
         * @return bool
         */
        public function valid()
        {
            return ($this->key() !== null);
        }
    }
}