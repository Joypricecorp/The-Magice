<?php
namespace Magice\Utils\Arrays {

    /**
     * Class TreePlot
     * @package     Magice\Utils\Arrays
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Plot
    {

        /**
         * @param      $arr
         * @param int  $indent
         * @param bool $mother_run
         *
         * @link http://kvz.io/blog/2007/10/03/convert-anything-to-tree-structures-in-php/
         */
        public static function run($arr, $indent = 0, $mother_run = true)
        {
            if ($mother_run) {
                // the beginning of plotTree. We're at rootlevel
                echo "start\n";
            }

            foreach ($arr as $k => $v) {
                // skip the baseval thingy. Not a real node.
                if ($k == "__base_val") {
                    continue;
                }
                // determine the real value of this node.
                $show_val = (is_array($v) ? $v["__base_val"] : $v);
                // show the indents
                echo str_repeat("  ", $indent);
                if ($indent == 0) {
                    // this is a root node. no parents
                    echo "O ";
                } elseif (is_array($v)) {
                    // this is a normal node. parents and children
                    echo "+ ";
                } else {
                    // this is a leaf node. no children
                    echo "- ";
                }

                // show the actual node
                echo $k . " (" . $show_val . ")" . "\n";
                if (is_array($v)) {
                    // this is what makes it recursive, rerun for childs
                    self::plot($v, ($indent + 1), false);
                }
            }

            if ($mother_run) {
                echo "end\n";
            }
        }
    }
}