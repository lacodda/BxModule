<?php

    namespace Lacodda\BxModule\Widget;

    use Lacodda\BxModule\Helper\Lang;

    /**
     * Виджет с числовыми значениями. Точная копия StringWidget, только работает с числами и не ищет по подстроке.
     */
    class NumberWidget
        extends StringWidget
    {
        /**
         * @var Lang
         */
        protected $lang;

        /**
         * @var array
         */
        static protected $defaults = array (
            'FILTER'       => '=',
            'EDIT_IN_LIST' => true,
        );

        /**
         * NumberWidget constructor.
         *
         * @param Lang $lang
         */
        public function __construct ()
        {
            $this->lang = new Lang(get_class ());
        }

        /**
         * @param string $operationType
         * @param mixed  $value
         *
         * @return bool
         */
        public function checkFilter ($operationType, $value)
        {
            return $this->isNumber ($value);
        }

        /**
         * @return bool
         */
        public function checkRequired ()
        {
            if ($this->getSettings ('REQUIRED') == true)
            {
                $value = $this->getValue ();

                return !is_null ($value);
            } else
            {
                return true;
            }
        }

        /**
         *
         */
        public function processEditAction ()
        {
            if (!$this->checkRequired ())
            {
                $this->addError ('REQUIRED_FIELD_ERROR');
            } else if (!$this->isNumber ($this->getValue ()))
            {
                $this->addError ('VALUE_IS_NOT_NUMERIC');
            }
        }

        /**
         * @param $value
         *
         * @return bool
         */
        protected function isNumber ($value)
        {
            return intval ($value) OR floatval ($value) OR doubleval ($value) OR is_null ($value) OR empty($value);
        }
    }