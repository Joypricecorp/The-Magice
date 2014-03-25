<?php
namespace Magice\Asset {

    use Magice\UI\Tag;

    /**
     * Class Manager
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     * @deprecated
     */
    class Manager
    {
        const ADD_TO_HEADER = 'header';
        const ADD_TO_FOOTER = 'footer';

        protected $scripts;
        protected $scriptsDeclared;
        protected $styles;
        protected $stylesDeclared;
        protected $appendTo = self::ADD_TO_HEADER;

        /**
         * @var Library $library
         */
        protected $library;

        public function __construct(Library $library)
        {
            $this->library = $library;
        }

        /**
         * Helper reset to append asset to header
         *
         * @param bool $flag
         *
         * @return $this
         */
        public function header($flag = true)
        {
            $this->appendTo = $flag ? self::ADD_TO_HEADER : self::ADD_TO_FOOTER;

            return $this;
        }

        /**
         * Helper reset to append asset to footer
         *
         * @param bool $flag
         *
         * @return $this
         */
        public function footer($flag = true)
        {
            $this->appendTo = $flag ? self::ADD_TO_FOOTER : self::ADD_TO_HEADER;

            return $this;
        }

        /**
         * Check is dev environment
         *
         * @param $str
         *
         * @return mixed|string
         * TODO: check from kerner.debug
         */
        function isDev($str)
        {
            if (defined('X-DEV') || $_SERVER['HTTP_HOST'] == 'localhost') {
                $str = preg_replace('/(\.\*min\.)/', '.', $str);

                if (!preg_match('/ext-all/', $str)) {
                    $str .= '?' . time();
                }

                return $str;
            } else {
                $str = preg_replace('/(\.\*min\.)/', '.min.', $str);
            }

            $str = preg_replace('/(\.dev\.)|(-dev\.)|(-debug\.)/', '.', $str);

            return $str;
        }

        /**
         * make sure file location is valid
         *
         * @param   string $file url of asset file
         *
         * @return string
         */
        protected function file($file)
        {
            return $file;
        }

        /**
         * Add script file
         *
         * @param string $file File path (full path)
         *                     TODO: add other property in array eg. array(async => true)
         *                     TODO: add second param to easy flag async
         *
         * @return $this
         */
        function script($file)
        {
            $this->scripts[$this->appendTo][$file] = array(
                'src'  => $file,
                'type' => 'text/javascript'
            );

            return $this;
        }

        /**
         * Declare script tag
         *
         * @param string $text
         *
         * @return $this
         */
        public function scriptDeclared($text)
        {
            $this->scriptsDeclared[$this->appendTo][] = array(
                'text' => $text,
                'type' => 'text/javascript'
            );

            return $this;
        }

        /**
         * Add stylesheet
         *
         * @param string $file Style file path (full path)
         * @param string $media
         *
         * @return $this
         */
        public function style($file, $media = 'all')
        {
            $this->styles[$this->appendTo][$file] = array(
                'rel'   => 'stylesheet',
                'type'  => 'text/css',
                'href'  => $file,
                'media' => $media
            );

            return $this;
        }

        /**
         * Declare style tag
         *
         * @param string $text
         * @param string $media
         * @param bool   $scoped
         *
         * @return $this
         */
        public function styleDeclared($text, $media = 'all', $scoped = false)
        {
            $this->stylesDeclared[$this->appendTo][] = array(
                'text'   => $text,
                'media'  => $media,
                'scoped' => $scoped
            );

            return $this;
        }

        /**
         * Import asset files <on demand>
         *
         * @param    string  $selector  path of selector
         * @param    boolean $ondemand  flag TRUE to load by document renderer, FALSE to fly on runtime (calling by ajax)
         * @param    string  $callback  javascript function name to fire when loaded (work only $ondemand = false)
         * @param    string  $namespace check existing namspace for onfly asset
         *
         * @return   $this
         */
        public function import($selector, $ondemand = true, $callback = null, $namespace = null)
        {
            //magic shift arg
            if ($ondemand === 'string') {
                $callback = $ondemand;
                $ondemand = true;
            }

            $assets = array();

            $local = strpos($selector, '//') !== false;

            if ($local) {
                $key  = 'js';
                $file = $selector;
                if (strpos($selector, '!')) {
                    list($key, $file) = explode('!', $selector);
                }

                $assets[$key][] = $file;

            } else {
                if (strpos($selector, '/') === false) {
                    $paths = explode('.', strtolower($selector));
                } else {
                    $paths = explode('/', strtolower($selector));
                }

                $sources = $this->library->sources;

                foreach ($paths as $path) {
                    $sources = $sources[$path];
                }

                if (is_array($sources) && array_key_exists('js', $sources)) {
                    if (is_array($sources['js'])) {
                        foreach ($sources['js'] as $f) {
                            $assets['js'][] = $this->isDev($this->file($f));
                        }
                    } else {
                        $assets['js'][] = $this->isDev($this->file($sources['js']));
                    }
                }

                if (is_array($sources) && array_key_exists('css', $sources)) {
                    if (is_array($sources['css'])) {
                        foreach ($sources['css'] as $f) {
                            $assets['css'][] = $this->isDev($this->file($f));
                        }
                    } else {
                        $assets['css'][] = $this->isDev($this->file($sources['css']));
                    }
                }

                // not config js/css key default to js (array format)
                if (is_array($sources) && !array_key_exists('js', $sources) && !array_key_exists('css', $sources)) {
                    foreach ($sources as $f) {
                        $assets['js'][] = $this->isDev($this->file($f));
                    }
                }

                // not config js/css key default to js (string format)
                if (!is_array($sources) && !empty($sources)) {
                    $assets['js'][] = $this->isDev($this->file($sources));
                }
            }

            if ($ondemand) {

                if (!empty($callback)) {
                    $this->scriptDeclared('$(function(){%s.call(window)})', $callback);
                }

                if (isset($assets['js'])) {
                    foreach ($assets['js'] as $f) {
                        $this->script($f);
                    }
                }
                if (isset($assets['css'])) {
                    foreach ($assets['css'] as $f) {
                        $this->style($f);
                    }
                }

            } else {
                if (!empty($callback)) {
                    $assets['cb'] = $callback;
                }

                if ($namespace) {
                    $assets['ns'] = $namespace;
                }

                $this->scriptDeclared('AssetsCALL(' . json_encode($assets) . ');');
            }

            return $this;
        }

        public function output($at = self::ADD_TO_HEADER)
        {
            $assets = array();

            // scripts
            if (!empty($this->scripts[$at])) {
                foreach ($this->scripts[$at] as $file) {
                    $assets[] = Tag::script($file);
                }
            }

            // styles
            if (!empty($this->styles[$at])) {
                foreach ($this->styles[$at] as $file) {
                    $assets[] = Tag::link($file);
                }
            }

            // scriptsDeclared
            if (!empty($this->scriptsDeclared[$at])) {
                foreach ($this->scriptsDeclared[$at] as $file) {
                    $assets[] = Tag::script($file);
                }
            }

            // stylesDeclared
            if (!empty($this->stylesDeclared[$at])) {
                foreach ($this->stylesDeclared[$at] as $file) {
                    $assets[] = Tag::style($file);
                }
            }

            return $assets;
        }

        /**
         * @param string|null $key clear with:
         *                         - all
         *                         - header
         *                         - footer
         *                         - script
         *                         - scripts (script & declared-script)
         *                         - style
         *                         - styles (style & declared-style)
         *                         - declared-script
         *                         - declared-style
         *                         ** use multiple key(s) with , to separate
         */
        public function clear($key = 'all')
        {
            switch ($key) {
                case 'all':
                    $this->scripts         = null;
                    $this->styles          = null;
                    $this->scriptsDeclared = null;
                    $this->stylesDeclared  = null;
                    break;

                case 'header':
                    $this->scripts[self::ADD_TO_HEADER]         = null;
                    $this->styles[self::ADD_TO_HEADER]          = null;
                    $this->scriptsDeclared[self::ADD_TO_HEADER] = null;
                    $this->stylesDeclared[self::ADD_TO_HEADER]  = null;
                    break;

                case 'footer':
                    $this->scripts[self::ADD_TO_FOOTER]         = null;
                    $this->styles[self::ADD_TO_FOOTER]          = null;
                    $this->scriptsDeclared[self::ADD_TO_FOOTER] = null;
                    $this->stylesDeclared[self::ADD_TO_FOOTER]  = null;
                    break;

                case 'script':
                    $this->scripts = null;
                    break;

                case 'scripts':
                    $this->scripts         = null;
                    $this->scriptsDeclared = null;
                    break;

                case 'style':
                    $this->styles = null;
                    break;

                case 'styles':
                    $this->styles         = null;
                    $this->stylesDeclared = null;
                    break;

                case 'declared-script':
                    $this->scriptsDeclared = null;
                    break;

                case 'declared-style':
                    $this->stylesDeclared = null;
                    break;
            }
        }
    }
}