<?php
namespace Magice\Mvc\View {

    use Magice\Utils\Classes\GetterSetter;

    /**
     * Class Section
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */

    /**
     * @method Section setStrict(bool $mode);
     * @method Section setName(string $name);
     * @method Section setPosition(int $index);
     * @method Section setContent(string $content);
     * @method string getName();
     * @method string getPosition();
     * @method string getContent();
     */
    class Section extends GetterSetter
    {
        protected $name;
        protected $position = 0;
        protected $content;

        /**
         * @param array $section
         *
         * @throws \InvalidArgumentException
         */
        public function __construct(array $section)
        {
            if (empty($section['name'])) {
                throw new \InvalidArgumentException('A "name" of section cannot be null.');
            }

            // apply section variables
            foreach ($section as $key => $val) {
                $this->$key = $val;
            }

            $this->setStrict(true);
        }
    }
}