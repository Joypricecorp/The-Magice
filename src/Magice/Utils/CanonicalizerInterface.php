<?php
namespace Magice\Utils {

    interface CanonicalizerInterface
    {
        /**
         * @param string $string
         *
         * @return string
         */
        public function canonicalize($string);
    }
}