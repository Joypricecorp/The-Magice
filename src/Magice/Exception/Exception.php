<?php
namespace Magice\Exception {

    //use Magice\Serviced\Param;

    class Exception extends \Exception implements \JsonSerializable
    {
        protected $messageUser;
        protected $messageDebug;
        protected $errorCode;

        protected static $isDebug = true;

        public function __construct($message, $code = 0, \Exception $e = null)
        {
            if ($message instanceof \Exception) {
                if (empty($code)) {
                    $code = $message->getCode();
                }

                $message = $message->getMessage();
            }

            if (is_array($message)) {
                if (isset($message['user'])) {
                    $this->messageUser = $message['user'];
                }

                if (isset($message['debug'])) {
                    $this->messageDebug = $message['debug'];
                }

                if (isset($message['code'])) {
                    $this->errorCode = $message['code'];
                }
            } else {
                $this->messageUser = $message;
            }

            if (empty($this->errorCode)) {
                $this->errorCode = $code;
            }

            if (static::$isDebug) {
                $message = trim(
                    sprintf(
                        '%s %s %s',
                        $this->errorCode ? ('[code] ' . $this->errorCode) : null,
                        $this->messageDebug ? ('[debug] ' . $this->messageDebug) : null,
                        $this->messageUser ? ('[user] ' . $this->messageUser) : null
                    )
                );
            } else {
                $message = $this->messageUser;
            }

            parent::__construct($message, $this->errorCode, $e);
        }

        /**
         * @param $flag
         */
        public static function setDebugMode($flag)
        {
            static::$isDebug = $flag;
        }

        /**
         * @return mixed
         */
        public function getMessageUser()
        {
            return $this->messageUser;
        }

        /**
         * @return mixed
         */
        public function getMessageDebug()
        {
            return $this->messageDebug;
        }

        public static function create($msg, $code = 0, \Exception $e = null)
        {
            return new static($msg, $code, $e);
        }

        public static function createMessage()
        {
            $msg = call_user_func_array('sprintf', func_get_args());

            return new static($msg, 0, null);
        }

        /**
         * (PHP 5 &gt;= 5.4.0)<br/>
         * Specify data which should be serialized to JSON
         * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         */
        public function jsonSerialize()
        {
            return array('code' => $this->getCode(), 'message' => $this->getMessage());
        }
    }
}
