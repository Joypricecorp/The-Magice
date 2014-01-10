<?php
namespace Magice\Utils {

    use Magice\Registry\Registry;
    use Zend\Barcode\Barcode as ZendBarcode;

    /**
     * Class Barcode
     * @package     Magice\Utils
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Barcode extends ZendBarcode
    {
        /**
         * Short-hand to Barcode::factory, and add array to support $barcode
         * Factory for Zend\Barcode classes.
         * First argument may be a string containing the base of the adapter class
         * name, e.g. 'int25' corresponds to class Object\Int25.  This
         * is case-insensitive.
         * First argument may alternatively be an object of type Traversable.
         * The barcode class base name is read from the 'barcode' property.
         * The barcode config parameters are read from the 'params' property.
         * Second argument is optional and may be an associative array of key-value
         * pairs.  This is used as the argument to the barcode constructor.
         * If the first argument is of type Traversable, it is assumed to contain
         * all parameters, and the second argument is ignored.
         *
         * @param  mixed $barcode              String name of barcode class, or associate array
         * @param  mixed $renderer             String name of renderer class
         * @param  mixed $barcodeConfig        OPTIONAL; an array or Traversable object with barcode parameters.
         * @param  mixed $rendererConfig       OPTIONAL; an array or Traversable object with renderer parameters.
         * @param  bool  $automaticRenderError OPTIONAL; set the automatic rendering of exception
         *
         * @return Barcode
         * @throws \Exception
         * @see http://framework.zend.com/manual/2.2/en/modules/zend.barcode.creation.html
         */
        public static function config(
            $barcode,
            $renderer = 'image',
            $barcodeConfig = array(),
            $rendererConfig = array(),
            $automaticRenderError = true
        ) {
            try {

                // support array config
                if (is_array($barcode)) {
                    $barcode = new Registry($barcode);
                }

                return parent::factory(
                    $barcode,
                    $renderer,
                    $barcodeConfig,
                    $rendererConfig,
                    $automaticRenderError
                );

            } catch (\Exception $e) {
                throw $e;
            }
        }
    }
}