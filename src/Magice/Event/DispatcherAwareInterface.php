<?php
namespace Magice\Event {

    interface DispatcherAwareInterface
    {
        /**
         * Sets EventDispatcher
         *
         * @param DispatcherInterface $dispatcher
         *
         * @return void
         */
        public function setDispatcher(DispatcherInterface $dispatcher);
    }
}