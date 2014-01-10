<?php
namespace Magice\Filesystem {

    use Magice\Client\Ftp;
    use Symfony\Component\Filesystem\Filesystem;

    /**
     * Class File
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class File extends Filesystem
    {
        protected $ftp;
        protected $file;

        public function __construct($file = null)
        {
            $this->file = $file;
        }

        // TODO:: implement Ftp

        /**
         * Set ftp connection
         *
         * @param Ftp $ftp
         */
        public function setFtp(Ftp $ftp)
        {
            $this->ftp = $ftp;
        }

        public function getContent($file = null)
        {
            return file_get_contents($file ? : $this->file);
        }
    }
}