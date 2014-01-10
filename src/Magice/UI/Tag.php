<?php
namespace Magice\UI {

    /**
     * Class Tag
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */

    /**
     * Methods by html tag(s)
     *
     * @method static a($textOrArray, array $attr = '')
     * @method static div($textOrArray, array $attr = '')
     * @method static input($textOrArray, array $attr = '')
     * @method static button($textOrArray, array $attr = '')
     * @method static span($textOrArray, array $attr = '')
     * @method static label($textOrArray, array $attr = '')
     * @method static script($textOrArray, array $attr = '')
     * @method static style($textOrArray, array $attr = '')
     * @method static meta($textOrArray, array $attr = '')
     * @method static link($textOrArray, array $attr = '')
     * @method static title($textOrArray, array $attr = '')
     */
    class Tag
    {
        private $tag;
        private $selfClosing = false;
        private $attrs = array();
        private $selfClosingList = array('input', 'img', 'hr', 'br', 'meta', 'link');

        /**
         * Create an html element
         * If you leave $selfClosing blank it will
         * determine whether or not to auto close
         *
         * @param string  $tag   - The tag's name div, input, form
         * @param array   $attrs - Attributes class, id
         * @param boolean $selfClosing
         */
        function __construct($tag, $attrs = array(), $selfClosing = null)
        {
            $this->tag = $tag;

            // force this tag to self close?
            if (is_null($selfClosing)) {
                $this->selfClosing = in_array($tag, $this->selfClosingList);
            } else {
                $this->selfClosing = $selfClosing;
            }

            // Make sure text is set
            $attrs['text'] = (empty($attrs['text'])) ? '' : $attrs['text'];

            $this->attrs = $attrs;

        }

        public static function __callStatic($name, $args)
        {
            if (method_exists(__CLASS__, $name)) {
                throw new \Exception(sprintf('Cannot call %s as static context.', $name));
            }

            if (is_string($args[0])) {
                $args[0] = array(
                    'text' => $args[0]
                );
            }

            if(isset($args[1])) {
                $args[0] = array_merge($args[1], $args[0]);
            }

            return (new self(
                $name,
                isset($args[0]) ? $args[0] : null
            ))->parsed();
        }

        /**
         * Build the html element
         * @see    $this->parsed()
         * @return string
         */
        public function __toString()
        {
            return $this->parsed();
        }

        /**
         * Add an attribute to the element
         * or multiple attributes if the first param is an array
         *
         * @param mixed  $attr
         * @param string $value
         *
         * @return void
         * @author Baylor Rae'
         */
        public function attr($attr, $value = null)
        {
            if (is_array($attr)) {
                $this->attrs = array_merge($this->attrs, $attr);
            } else {
                $this->attrs = array_merge($this->attrs, array($attr => $value));
            }
        }

        /**
         * Creates the html element's opening and closing tags
         * as well as the attributes.
         * @return string
         */
        public function parsed()
        {

            // Start the tag
            $output = '<' . $this->tag;

            // Add the attributes
            foreach ($this->attrs as $attr => $value) {
                if ($attr == 'text') {
                    continue;
                }

                if (is_integer($attr)) {
                    $attr = $value;
                }
                $output .= ' ' . $attr . '="' . $value . '"';
            }

            // Close the tag
            if ($this->selfClosing) {
                $output .= ' />';
            } else {
                $output .= '>' . $this->attrs['text'] . '</' . $this->tag . '>';
            }

            return $output;
        }

        /**
         * Clone the current element
         * to prevent effecting the original
         * @return $this
         */
        public function _clone()
        {
            return new Tag($this->tag, $this->attrs, $this->selfClosing);
        }

        /**
         * Check if the object being used
         * in the methods below, is part of the html class
         *
         * @param $obj
         *
         * @return bool
         */
        private function checkClass($obj)
        {
            return (@get_class($obj) == __CLASS__);
        }

        /**
         * Append an element to the current
         * or for multiple use each element
         * as a parameter
         * @return $this
         */
        public function append()
        {
            $elems = func_get_args();

            /**
             * @var Tag $obj
             */
            foreach ($elems as $obj) {
                if (!$this->checkClass($obj)) {
                    continue;
                }

                $this->attrs['text'] .= $obj->parsed();
            }

            return $this;
        }

        /**
         * Prepend an element to the current
         * or for multiple use each element
         * as a parameter
         * @return $this
         */
        public function prepend()
        {
            $elems = func_get_args();

            $elems = array_reverse($elems);

            /**
             * @var Tag $obj
             */
            foreach ($elems as $obj) {
                if (!$this->checkClass($obj)) {
                    continue;
                }

                $this->attrs['text'] = $obj->parsed() . $this->attrs['text'];
            }

            return $this;
        }

        /**
         * Append this element onto another
         *
         * @param object $obj
         *
         * @return void
         */
        public function appendTo($obj)
        {
            if (!$this->checkClass($obj)) {
                return;
            }

            $obj->attrs['text'] .= $this->parsed();
        }

        /**
         * Prepend this element onto another
         *
         * @param object $obj
         *
         * @return void
         */
        public function prependTo($obj)
        {
            if (!$this->checkClass($obj)) {
                return;
            }

            $obj->attrs['text'] = $this->parsed() . $obj->attrs['text'];
        }
    }
}