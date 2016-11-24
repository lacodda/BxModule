<?php

    namespace Lacodda\BxModule\Bitrix;

    /**
     * Class User
     *
     * @package Lacodda\StaffTools
     */
    class User
        extends \CUser
    {
        /**
         * @var array
         */
        public static $user = [];

        /**
         * @var array
         */
        public $filtered = [];

        /**
         * @param null $id
         *
         * @return User|static
         */
        public static function find ($id = null)
        {
            if (is_array ($id))
            {
                return self::findMany ($id);
            }

            $id = $id ? $id : self::GetId ();

            $user = self::GetByID ($id);

            self::$user[$id] = $user->Fetch ();

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

            $rsUsers = self::GetList ($by = ['last_name' => 'asc', 'login' => 'desc'], $order = 'asc', $filter);

            $user = [];

            while ($arUser = $rsUsers->Fetch ())
            {
                $user[$arUser['ID']] = $arUser;
            }

            self::$user = $user;

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

            $name = array_map (
                function ($n)
                {
                    $array = array_values (array_diff (preg_split ('/\s+/', $n), ['']));

                    return sprintf ('%s&%s', $array[0], $array[1]);
                },
                $name
            );

            $filter = ['NAME' => implode ('|', $name)];

            $rsUsers = self::GetList ($by = ['last_name' => 'asc', 'login' => 'desc'], $order = 'asc', $filter);

            $user = [];

            while ($arUser = $rsUsers->Fetch ())
            {
                $user[$arUser['ID']] = $arUser;
            }

            self::$user = $user;

            $instance = new static;

            return $instance;
        }

        /**
         * @param array  $groups_id
         * @param array  $id
         * @param string $active
         *
         * @return static
         */
        public static function where (array $groups_id = [], array $id = [], $active = 'Y')
        {
            $filter = [];
            $filter = !empty($groups_id) ? array_merge ($filter, ['GROUPS_ID' => $groups_id]) : $filter;
            $filter = !empty($id) ? array_merge ($filter, ['ID' => implode ('|', $id)]) : $filter;
            $filter = array_merge ($filter, ['ACTIVE' => $active]);

            $rsUsers = self::GetList ($by = ['last_name' => 'asc', 'login' => 'desc'], $order = 'asc', $filter);

            $user = [];

            while ($arUser = $rsUsers->Fetch ())
            {
                $user[$arUser['ID']] = $arUser;
            }

            self::$user = $user;

            $instance = new static;

            return $instance;
        }

        /**
         * @return $this
         */
        public function get ()
        {
            $this->filtered = self::$user;

            return $this;
        }

        /**
         * @return $this
         */
        public function id ()
        {
            $this->filtered = Helper::arrayColumn (self::$user, 'ID');

            return $this;
        }

        /**
         * @param bool   $initials
         * @param string $lang
         *
         * @return $this
         */
        public function name ($initials = false, $lang = 'ru')
        {
            $name = [];
            $lastName = [];
            $secondName = [];
            $id = Helper::arrayColumn (self::$user, 'ID');
            $login = Helper::arrayColumn (self::$user, 'LOGIN');

            if ($lang == 'ru')
            {
                $name = Helper::arrayColumn (self::$user, 'NAME');
                $lastName = Helper::arrayColumn (self::$user, 'LAST_NAME');
                $secondName = Helper::arrayColumn (self::$user, 'SECOND_NAME');
            } elseif ($lang == 'en')
            {
                $name = Helper::arrayColumn (self::$user, 'UF_NAME_EN');
                $lastName = Helper::arrayColumn (self::$user, 'UF_LASTNAME_EN');
                $secondName = Helper::arrayColumn (self::$user, 'UF_SECONDNAME_EN');
            }

            $nullNames = array_keys ($name, null);

            if (!empty($nullNames))
            {
                $name = array_replace ($name, array_intersect_key ($login, array_flip ($nullNames)));
            }

            $userName = array_map (
                function ($name, $lastName, $secondName) use (&$initials)
                {
                    if ($initials)
                    {
                        $name = $name ? sprintf ('%s.', mb_substr (trim ($name), 0, 1)) : '';
                        $secondName = $secondName ? sprintf ('%s.', mb_substr (trim ($secondName), 0, 1)) : '';
                    }

                    $userName = $lastName ? sprintf ('%s %s', $lastName, $name) : $name;
                    $userName = $secondName ? sprintf ('%s %s', $userName, $secondName) : $userName;

                    return $userName;
                },
                $name,
                $lastName,
                $secondName
            );

            $this->filtered = array_combine ($id, $userName);

            return $this;
        }

        /**
         * @return $this
         */
        public function email ()
        {
            $this->filtered = Helper::arrayColumn (self::$user, 'EMAIL');

            return $this;
        }

        /**
         * @return $this
         */
        public function phone ()
        {
            $this->filtered = Helper::arrayColumn (self::$user, 'PHONE');

            return $this;
        }

        /**
         * @return $this
         */
        public function work_phone ()
        {
            $this->filtered = Helper::arrayColumn (self::$user, 'WORK_PHONE');

            return $this;
        }

        /**
         * @return $this
         */
        public function sap ()
        {
            $this->filtered = Helper::arrayColumn (self::$user, 'UF_1C_PR4CD071EFA634');

            return $this;
        }

        /**
         * @return $this
         */
        public function passport ()
        {
            $this->filtered = Helper::arrayColumn (self::$user, 'UF_RUS_PASSPORT');

            return $this;
        }

        /**
         * @return $this
         */
        public function int_passport ()
        {
            $this->filtered = Helper::arrayColumn (self::$user, 'UF_INT_PASSPORT');

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