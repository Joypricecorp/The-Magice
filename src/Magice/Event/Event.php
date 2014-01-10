<?php
namespace Magice\Event {

    use Symfony\Component\EventDispatcher\GenericEvent;
    use Symfony\Component\EventDispatcher\EventDispatcherInterface;

    /**
     * Event
     *
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Event extends GenericEvent
    {
        /**
         * Stops the propagation of the event to further event listeners.
         *
         * If multiple event listeners are connected to the same event, no
         * further event listener will be triggered once any trigger calls
         * stop().
         *
         */
        public function stop()
        {
            $this->stopPropagation();
        }

        /**
         * Returns whether further event listeners should be triggered.
         *
         * @see Event::stop
         * @return Boolean Whether propagation was already stopped for this event.
         *
         */
        public function stopped()
        {
            return $this->isPropagationStopped();
        }
    }
}