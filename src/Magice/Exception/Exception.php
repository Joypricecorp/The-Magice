<?php
namespace Magice\Exception {

    //use Magice\Serviced\Param;

    class Exception extends \Exception
    {
        protected $defaultMessage = 'Oops! An error eccurred. Please try again later.';

        public function __construct($message, $code = 0)
        {
            $debug       = true;//Param::get('kernel.debug');
            $messageCode = null;

            if (is_array($message)) {

                if (!empty($message['code'])) {
                    $messageCode = $message['code'];
                }

                switch (true) {
                    case $debug:
                        $message = isset($message['debug']) ? $message['debug'] : (isset($message['user']) ? $message['user'] : $this->defaultMessage);
                        break;
                    default:
                        $message = (isset($message['user'])) ? $message['user'] : $this->defaultMessage;
                        break;
                }
            }

            // TODO: parse message with: :[user] xxxx :[debug] xxxxx

            if ($messageCode) {
                $message = sprintf('[%s] %s', $messageCode, $message);
            }

            parent::__construct($message, $code);
        }
    }
}
