<?php
namespace Magice\Event {

    class DispatcherAware implements DispatcherAwareInterface
    {
        /**
         * @var DispatcherInterface $dispatcher
         */
        protected $dispatcher;

        /**
         * @var string $prefix Event prefix
         */
        protected $prefix;

        /**
         * @var object $scope Scope of listeners
         */
        protected $scope;

        protected $events;
        protected $listeners;

        /**
         * Sets EventDispatcher
         *
         * @param DispatcherInterface $dispatcher
         */
        final public function setDispatcher(DispatcherInterface $dispatcher)
        {
            $this->dispatcher = $dispatcher;
        }

        /**
         * Sets event prefix
         *
         * @param string $prefix Event prefix name
         */
        final public function prefix($prefix)
        {
            $this->prefix = $prefix;
        }

        final public function scope($scope)
        {
            $this->scope = $scope;
        }

        /**
         * @param string $eventName
         * @param Event  $event
         *
         * @return Event
         */
        final public function fire($eventName, Event $event = null)
        {
            if ($this->prefix) {
                $eventName = $this->prefix . '.' . $eventName;
            }

            return $this->dispatcher->dispatch($eventName, $event);
        }

        public function events(){}
        public function listeners(){}
    }
}