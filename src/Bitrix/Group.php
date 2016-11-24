<?php

    namespace Lacodda\BxModule\Bitrix;

    /**
     * Class Group
     *
     * @package Lacodda\StaffTools
     */
    class Group
        extends \CGroup
    {
        /**
         * @var array
         */
        public static $group = [];

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

            $group = self::GetByID ($id);

            self::$group[$id] = $group->Fetch ();

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

            $rsGroups = self::GetList ($by = 'c_sort', $order = 'asc', $filter);

            $group = [];

            while ($arGroup = $rsGroups->Fetch ())
            {
                $group[$arGroup['ID']] = $arGroup;
            }

            self::$group = $group;

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

            $rsGroups = self::GetList ($by = 'c_sort', $order = 'asc', $filter);

            $group = [];

            while ($arGroup = $rsGroups->Fetch ())
            {
                $group[$arGroup['ID']] = $arGroup;
            }

            self::$group = $group;

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

            $rsGroups = self::GetList ($by = 'c_sort', $order = 'asc', $filter);

            $group = [];

            while ($arGroup = $rsGroups->Fetch ())
            {
                $group[$arGroup['ID']] = $arGroup;
            }

            self::$group = $group;

            $instance = new static;

            return $instance;
        }

        /**
         * @return $this
         */
        public function get ()
        {
            $this->filtered = self::$group;

            return $this;
        }

        /**
         * @return $this
         */
        public function id ()
        {
            $this->filtered = Helper::arrayColumn (self::$group, 'ID');

            return $this;
        }

        /**
         * @return $this
         */
        public function name ()
        {
            $this->filtered = Helper::arrayColumn (self::$group, 'NAME');

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
    }