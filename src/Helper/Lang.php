<?php

    namespace Lacodda\BxModule\Helper;

    /**
     * Class Lang
     *
     * @package Lacodda\BxModule\Helper
     */
    class Lang
    {
        /**
         * @var
         */
        private $className;

        /**
         * @var
         */
        private $classNameLang;

        /**
         * Lang constructor.
         *
         * @param $className
         */
        public function __construct ($className)
        {
            $this->setClassName ($className);
            $this->setClassNameLang ($className);
        }

        /**
         * @param mixed $className
         */
        public function setClassName ($className)
        {
            $this->className = $className;
        }

        /**
         * @param mixed $classNameLang
         */
        public function setClassNameLang ($className)
        {
            $this->classNameLang = self::getLangClass ($className);
        }

        /**
         * @param      $code
         * @param null $replace
         * @param null $language
         *
         * @return mixed|string
         */
        public function getMessage ($code, $replace = null, $language = null)
        {
            foreach ($this->classNameLang as $classLang)
            {
                $lang = new $classLang;

                if (isset($lang->mess[$code]))
                {
                    $s = $lang->mess[$code];
                    break;
                }

                $s = $code;
            }

            $codeCharset = strtolower (mb_detect_encoding ($s));

            $siteCharset = strtolower (LANG_CHARSET);

            if ($codeCharset != $siteCharset)
            {
                $s = self::convertCharset ($s, $codeCharset, $siteCharset);
            }

            if ($replace !== null && is_array ($replace))
            {
                foreach ($replace as $search => $repl)
                {
                    $s = str_replace ($search, $repl, $s);
                }
            }

            return $s;
        }

        /**
         * @param $str
         * @param $from
         * @param $to
         *
         * @return mixed|string
         */
        public static function convertCharset ($str, $from, $to)

        {
            if (is_array ($str))
            {
                return self::convertCharsetArray ($str);
            } else
            {
                return iconv ($from, $to, $str);
            }
        }

        /**
         * @param $array
         * @param $from
         * @param $to
         *
         * @return mixed
         */
        public static function convertCharsetArray ($array, $from, $to)
        {
            array_walk_recursive (
                $array,
                function (&$item, &$key)
                {
                    $item = is_string ($item) ? iconv ($from, $to, $item) : $item;
                }
            );

            return $array;
        }

        /**
         * @param $name
         *
         * @return array
         */
        public static function parseClassname ($name)
        {
            return array (
                'namespace' => array_slice (explode ('\\', $name), 0, - 1),
                'classname' => join ('', array_slice (explode ('\\', $name), - 1)),
            );
        }

        /**
         * @param $name
         *
         * @return array|bool
         */
        public static function getLangClass ($name)
        {
            if (class_exists ($name))
            {
                $classTree = class_parents ($name);

                array_unshift ($classTree, $name);

                $classLangTree = [];

                foreach ($classTree as $name)
                {
                    $class = self::parseClassname ($name);

                    $classLang = sprintf ('%s\\%s\\%s', implode ('\\', $class['namespace']), 'Lang', $class['classname']);

                    if (class_exists ($classLang))
                    {
                        $classLangTree[] = $classLang;
                    }
                }

                return $classLangTree;
            } else
            {
                return false;
            }
        }
    }