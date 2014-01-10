<?php
namespace Magice\Bundle\Symfony\AutoConfiguration {

    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template as BaseTemplate;

    /**
     * Class Template
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */

    /**
     * @Annotation
     */
    class Template extends BaseTemplate
    {
        /**
         * This allow to add css name from annon method into page scope (html class tag)
         * @var string
         */
        protected $scoped = '';

        /**
         * This allow to add css name from annon method into page scope (html class tag)
         * @var string
         */
        protected $format = 'html';

        /**
         * An array of support view formats.
         * @var array
         */
        protected $formats = array();

        /**
         * Get output format
         * @return string
         */
        public function getFormat()
        {
            return $this->format;
        }

        /**
         * Set output format
         *
         * @param string $str
         */
        public function setFormat($str)
        {
            $this->format = $str;
        }

        /**
         * Get css page scope
         * @return string
         */
        public function getScoped()
        {
            return $this->scoped;
        }

        /**
         * Set css page scope
         *
         * @param string $str
         */
        public function setScoped($str)
        {
            $this->scoped = $str;
        }

        /**
         * Returns the array of support view formats.
         * @return array
         */
        public function getFormats()
        {
            return $this->formats;
        }

        /**
         * Sets the support view formats.
         *
         * @param array|string $formats A support output format or an array of support formats
         */
        public function setFormats($formats)
        {
            $this->formats = is_array($formats) ? $formats : array($formats);
        }
    }
}