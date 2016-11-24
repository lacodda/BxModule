<?php

    namespace Lacodda\BxModule\Bitrix;

    use Carbon\Carbon;

    /**
     * Class Helper
     *
     * @package Lacodda\StaffTools
     */
    Class Helper
    {
        /**
         * @var array
         */
        private static $serverPortException = array (80, 443);

        /**
         * @param bool|false $document_uri
         *
         * @return string
         */
        public static function curPageUrl ($document_uri = false)
        {
            $pageURL = 'http';
            if ($_SERVER["HTTPS"] == "on")
            {
                $pageURL .= "s";
            }
            $pageURL .= "://";
            $path = $_SERVER["REQUEST_URI"];
            if ($document_uri)
            {
                $parse_url = parse_url ($path);
                //				$path = $_SERVER["DOCUMENT_URI"];
                $path = $parse_url['path'];
            }
            if (!in_array ($_SERVER["SERVER_PORT"], self::$serverPortException))
            {
                $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $path;
            } else
            {
                $pageURL .= $_SERVER["SERVER_NAME"] . $path;
            }

            return $pageURL;
        }

        /**
         * @return mixed
         */
        public static function getCountryArray ()
        {
            $arrCountry = GetCountryArray ();
            array_unshift ($arrCountry['reference'], '<выбирите значение>');

            return $arrCountry['reference'];
        }

        /**
         * @param $id
         *
         * @return string
         */
        public static function getCountryById ($id)
        {
            try
            {
                $arrCountry = self::getCountryArray ();

                return $arrCountry[$id];
            } catch (\Exception $e)
            {
                return $e->getMessage ();
            }
        }

        /**
         * @param $item
         *
         * @return string
         */
        public static function convertToCp1251 ($item)

        {
            if (is_array ($item))
            {
                return self::convertArrayToCp1251 ($item);
            } else
            {
                return iconv ('utf-8', 'windows-1251', $item);
            }
        }

        /**
         * @param $array
         *
         * @return mixed
         */
        public static function convertArrayToCp1251 ($array)
        {
            array_walk_recursive (
                $array,
                function (&$item, &$key)
                {
                    $item = is_string ($item) ? iconv ('utf-8', 'windows-1251', $item) : $item;
                }
            );

            return $array;
        }

        /**
         * @param $item
         *
         * @return string
         */
        public static function convertToUtf8 ($item)
        {
            if (is_array ($item))
            {
                return self::convertArrayToUtf8 ($item);
            } else
            {
                return iconv ('windows-1251', 'utf-8', $item);
            }
        }

        /**
         * @param $array
         *
         * @return mixed
         */
        public static function convertArrayToUtf8 ($array)
        {
            array_walk_recursive (
                $array,
                function (&$item, &$key)
                {
                    $item = is_string ($item) ? iconv ('windows-1251', 'utf-8', $item) : $item;
                }
            );

            return $array;
        }

        /**
         * @param $array
         * @param $column_name
         *
         * @return array
         */
        public static function arrayColumn ($array, $column_name)
        {
            return array_map (
                function ($element) use ($column_name)
                {
                    if (array_key_exists ($column_name, $element))
                    {
                        return $element[$column_name];
                    }
                },
                $array
            );
        }

        /**
         * @param $array
         *
         * @return array
         */
        public static function arrayGroup (array $array, array $merged_array = [])
        {
            $result = array ();

            foreach ($array as $key => $value)
            {
                foreach ($value as $k => $v)
                {
                    $result[$k][$key] = $v;

                    if (!empty($merged_array))
                    {
                        $result[$k] = array_merge ($result[$k], $merged_array);
                    }
                }
            }

            return $result;
        }

        public static function arrayGroup2 ($array)
        {
            $result = self::arrayGroup ($array);

            foreach ($array as $key => $value)
            {
                if (!is_array ($value))
                {
                    foreach ($result as $resultKey => $resultVal)
                    {
                        $result[$resultKey][$key] = $value;
                    }
                }
            }

            return $result;
        }

        /**
         * @param $array
         * @param $name
         * @param $val
         *
         * @return bool
         */
        function arrayFilter ($array, $name, $val)
        {
            $Arr = array_filter (
                $array,
                function ($var) use ($name, $val)
                {
                    return ($var[$name] == $val);
                }
            );

            return $Arr ? true : false;
        }

        // Возвращает массив преобразуя струку получаемую в качестве параметра
        /**
         * @param        $str
         * @param string $delimiter
         *
         * @return array
         */
        public static function getArrByStr ($str, $delimiter = '\s*(,|;)\s*')
        {
            return array_values (array_diff (preg_split ('/' . $delimiter . '/', $str), ['']));
        }

        /**
         * @param $time
         *
         * @return bool|string
         */
        public static function getDateFormat ($time)
        {
            return date ('d.m.Y', $time);
        }

        /**
         * @param            $money
         * @param bool|false $money_cur
         *
         * @return string
         */
        public static function getMoneyFormat ($money, $money_cur = false)
        {
            $money_cur = $money_cur ? ' ' . self::getCurrencySign ($money_cur) : '';

            return number_format ($money, 2, ',', ' ') . $money_cur;
        }

        public static function getVariable ($name)
        {
            if (is_callable ('self::' . $name))
            {
                return call_user_func ('self::' . $name);
            } else
            {
                return Variables::${$name};
            }
        }

        public static function getArrByStatusId ($status_id, $object)
        {
            $rsData = $object->getList (array ('filter' => array ('REQUEST_STATUS_ID' => $status_id),));

            $rows = array ();

            while ($row = $rsData->fetch ())
            {
                $rows[$row['ID']] = $row;
            }

            return $rows;
        }

        public static function getArrFieldsList ()
        {
            return FieldsTable::getNameList ();
        }

        public static function getArrStatusesList ()
        {
            return RequestStatusesTable::getArr ();
        }

        public static function getArrClassPlane ()
        {
            return Variables::$arrTransportType[1]['CLASS'];
        }

        public static function getArrClassTrain ()
        {
            return Variables::$arrTransportType[2]['CLASS'];
        }

        public static function getArrCurrencyId ()
        {
            return self::arrayColumn (Variables::$arrCurrency, 'ID');
        }

        public static function getArrCurrencyFull ()
        {
            return self::arrayColumn (Variables::$arrCurrency, 'FULL');
        }

        public static function getCurrencyFull ($id)
        {
            $currency = self::getArrCurrencyFull ();

            return $currency[$id];
        }

        public static function getArrCurrencySign ()
        {
            return self::arrayColumn (Variables::$arrCurrency, 'SIGN');
        }

        public static function getCurrencySign ($id)
        {
            $currency = self::getArrCurrencySign ();

            return $currency[$id];
        }

        public static function getArrCostCenter ()
        {

            return CommitmentTable::getListCostCenter ();
        }

        public static function getArrCostElement ()
        {
            return array ('') + CommitmentTable::getListCostElement ();
        }

        public static function getArrCommitment ()
        {
            return array ('') + CommitmentTable::getListCommitment ();
        }
    }