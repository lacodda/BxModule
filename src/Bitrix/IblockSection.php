<?php

    namespace Lacodda\BxModule\Bitrix;

    use Bitrix\Iblock\SectionTable;
    use Bitrix\Main\Loader;

    use Lacodda\BxModule\Helper\Lang;

    /**
     *
     * @author Kirill Lahtachev <lahtachev@gmail.com>
     */
    class IblockSection
        extends SectionTable

    {

        /**
         * @var array
         */
        public static $section = [];

        /**
         * @var array
         */
        public $filtered = [];

        /**
         * @param $id
         *
         * @return Group|static
         */
        public static function find ($id)
        {
            if (is_array ($id))
            {
                return self::findMany ($id);
            }

            $section = self::GetByID ($id);

            self::$section[$id] = $section->Fetch ();

            $instance = new static;

            return $instance;
        }

        /**
         * @param array $id
         *
         * @return static
         */
        private static function findMany (array $id)
        {
            $filter = ['ID' => implode ('|', $id)];

            $rsSection = SectionTable::getList (
                [
                    'filter' => [
                        'ID' => $sectionId,
                    ],
                    'select' => [
                        'ID',
                        'NAME',
                        'IBLOCK_ID',
                        'IBLOCK.IBLOCK_TYPE_ID',
                    ],
                ]
            );

            $section = $rsSection->fetch ();

            $section = [];

            while ($arSection = $rsSections->Fetch ())
            {
                $section[$arSection['ID']] = $arSection;
            }

            self::$section = $section;

            $instance = new static;

            return $instance;
        }

        /**
         * @param $name
         *
         * @return static
         */
        public static function findByName ($name)
        {
            if (!is_array ($name))
            {
                $name = [$name];
            }

            $filter = ['NAME' => implode ('|', $name)];

            $rsSections = self::GetList ($by = 'c_sort', $order = 'asc', $filter);

            $section = [];

            while ($arSection = $rsSections->Fetch ())
            {
                $section[$arSection['ID']] = $arSection;
            }

            self::$section = $section;

            $instance = new static;

            return $instance;
        }

        /**
         * @param array  $id
         * @param string $active
         *
         * @return static
         */
        public static function where (array $id = [], $active = 'Y')
        {
            $filter = [];
            $filter = !empty($id) ? array_merge ($filter, ['ID' => implode ('|', $id)]) : $filter;
            $filter = array_merge ($filter, ['ACTIVE' => $active]);
            $filter = ['IBLOCK_ID' => 5];

            //            $rsSections = \CIBlockSection::GetList(array(), $arFilter);
            //            while ($arSection = $rsSections->Fetch())
            //            {
            //                var_dump($arSection['NAME'].$arSection['DEPTH_LEVEL']);
            //            }

            $rsSections = \CIBlockSection::getList (array (), $filter);

            $section = [];

            while ($arSection = $rsSections->Fetch ())
            {
                $section[$arSection['ID']] = $arSection;
            }

            self::$section = $section;

            $instance = new static;

            return $instance;
        }

        /**
         * @return $this
         */
        public function get ()
        {
            $this->filtered = self::$section;

            return $this;
        }

        /**
         * @return $this
         */
        public function id ()
        {
            $this->filtered = Helper::arrayColumn (self::$section, 'ID');

            return $this;
        }

        /**
         * @return $this
         */
        public function name ()
        {
            $this->filtered = Helper::arrayColumn (self::$section, 'NAME');

            return $this;
        }

        /**
         * @return $this
         */
        public function hierarchy ($sep = '.')
        {
            $id = Helper::arrayColumn (self::$section, 'ID');
            $depthLevel = Helper::arrayColumn (self::$section, 'DEPTH_LEVEL');
            $name = Helper::arrayColumn (self::$section, 'NAME');

            $hierarchy = array_map (
                function ($depthLevel, $name) use (&$sep)
                {
                    return sprintf ('%s %s', str_repeat ($sep, $depthLevel), $name);
                },
                $depthLevel,
                $name
            );

            $this->filtered = array_combine ($id, $hierarchy);

            return $this;
        }

        /**
         * @return array
         */
        public function all ()
        {
            return $this->filtered;
        }

        /**
         * @return mixed
         */
        public function first ()
        {
            return array_shift ($this->filtered);
        }

        public static function getSectionList ($filter, $select)
        {
            $dbSection = \CIBlockSection::GetList (
                Array (
                    'LEFT_MARGIN' => 'ASC',
                ),
                array_merge (
                    Array (
                        'ACTIVE'        => 'Y',
                        'GLOBAL_ACTIVE' => 'Y',
                    ),
                    is_array ($filter) ? $filter : Array ()
                ),
                false,
                array_merge (
                    Array (
                        'ID',
                        'IBLOCK_SECTION_ID',
                    ),
                    is_array ($select) ? $select : Array ()
                )
            );

            while ($arSection = $dbSection->GetNext (true, false))
            {

                $SID = $arSection['ID'];
                $PSID = (int) $arSection['IBLOCK_SECTION_ID'];

                $arLincs[$PSID]['CHILDS'][$SID] = $arSection;

                $arLincs[$SID] = &$arLincs[$PSID]['CHILDS'][$SID];
            }

            return array_shift ($arLincs);
        }
    }

    
