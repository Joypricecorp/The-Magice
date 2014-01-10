<?php
namespace Magice\Utils {

    interface TokenGeneratorInterface
    {
        /**
         * @return string
         */
        public function generateToken();
    }

}