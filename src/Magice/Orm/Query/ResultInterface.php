<?php
namespace Magice\Orm\Query {
    interface ResultInterface
    {
        public function getArrays($asColumn = false);

        public function getPlains($asColumn = false);

        public function getObjects();

        public function getPaginArrays($asColumn = false);

        public function getPaginPlains($asColumn = false);

        public function getPaginObjects();
    }
}