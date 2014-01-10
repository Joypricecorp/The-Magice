<?php
namespace Magice\Mvc\View {

    /**
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Sections
    {
        const TAG_HEADER          = 'HEADER';
        const TAG_SECTION         = 'SECTION';
        const TAG_SECTION_CONTENT = 'CONTENT';
        const TAG_SCRIPT          = 'SCRIPT';

        /**
         * @var Section[] $sections
         */
        protected $sections;

        /**
         * Check exiting section-tag-name
         *
         * @param string $sectionTag
         *
         * @return bool
         */
        public function has($sectionTag)
        {
            return array_key_exists($sectionTag, $this->sections);
        }

        /**
         * Set new section into section-tag-name
         *
         * @param string  $sectionTag
         * @param Section $section
         */
        public function set($sectionTag, Section $section)
        {
            $this->sections[$sectionTag][] = $section;
        }

        /**
         * Remove sections with in section-tag-name
         *
         * @param string $sectionTag
         */
        public function remove($sectionTag)
        {
            if ($this->has($sectionTag)) {
                unset($this->sections[$sectionTag]);
            }
        }

        /**
         * Set/Replace section by id
         *
         * @param string      $id
         * @param Section     $section
         * @param string|null $sectionTag
         */
        public function setAt($id, Section $section, $sectionTag = null)
        {
            if ($found = $this->find($id, $sectionTag)) {
                $this->sections[$found['section']][$found['index']] = $section;
            } else {
                $this->set($sectionTag, $section);
            }
        }

        /**
         * Remove section by id
         *
         * @param string      $id
         * @param string|null $sectionTag
         */
        public function removeAt($id, $sectionTag = null)
        {
            if ($found = $this->find($id, $sectionTag)) {
                unset($this->sections[$found['section']][$found['index']]);
            }
        }

        /**
         * Get sections with in section-tag-name
         *
         * @param string $sectionTag
         *
         * @return Section[]|null
         */
        function get($sectionTag)
        {
            if ($this->has($sectionTag)) {
                return $this->sections[$sectionTag];
            }

            return null;
        }

        /**
         * Get section by id
         *
         * @param string      $id
         * @param string|null $sectionTag
         *
         * @return null|Section
         */
        function getAt($id, $sectionTag = null)
        {
            if ($found = $this->find($id, $sectionTag)) {
                return $this->sections[$found['section']][$found['index']];
            }

            return null;
        }

        /**
         * Find section index by id
         *
         * @param string      $id
         * @param string|null $sectionTag
         *
         * @return null|array Null or Array with: index, section-tag
         */
        function find($id, $sectionTag = null)
        {
            $find = function ($id, $sectionTag, $sections) {
                if (array_key_exists($sectionTag, $sections)) {
                    /**
                     * @var Section $section
                     */
                    foreach ($sections[$sectionTag] as $index => $section) {
                        if ($section->getName() == $id) {
                            return array(
                                'index'   => $index,
                                'section' => $sectionTag
                            );
                        }
                    }
                }

                return null;
            };

            if ($sectionTag) {
                return $find($id, $sectionTag, $this->sections);
            } else {
                foreach ($this->sections as $sectionTag) {
                    if ($found = $find($id, $sectionTag, $this->sections)) {
                        return $found;
                    }
                }
            }

            return null;
        }

        public function sortAt($sectionTag)
        {
            if (!$this->has($sectionTag)) {
                return null;
            }

            $sections = array();

            /**
             * @var Section $section
             */
            foreach ($this->get($sectionTag) as $section) {
                $max = empty($sections) ? 0 : max(array_keys($sections));
                if ($section->getPosition()) {
                    if (array_key_exists($section->getPosition(), $sections)) {
                        $sections[$max] = $section;
                    } else {
                        $sections[$section->getPosition()] = $section;
                    }
                } else {
                    $sections[$max] = $section;
                }
            }

            ksort($sections);

            $this->sections[$sectionTag] = $sections;
        }

        public function loadSectionContent($sectionTag)
        {
            if (!$this->has($sectionTag)) {
                return null;
            }

            //$this->sortAt($sectionTag); // TODO: sort by position now not correct

            $content = '';
            /**
             * @var Section $section
             */
            foreach ($this->get($sectionTag) as $section) {
                $content .= $section->getContent();
            }

            return $content;
        }
    }
}