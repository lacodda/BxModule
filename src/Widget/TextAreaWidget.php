<?php

    namespace Lacodda\BxModule\Widget;

    use Lacodda\BxModule\Helper\Lang;

    /**
     * Выводит textarea для редактирования длинных строк.
     * Урезает длинные строки при отображении в списке
     *
     * Доступные опции:
     * <ul>
     * <li><b>COLS</b> - ширина</li>
     * <li><b>ROWS</b> - высота</li>
     * </ul>
     */
    class TextAreaWidget
        extends StringWidget
    {
        /**
         * @var Lang
         */
        protected $lang;

        /**
         * количество отображаемых символов в режиме списка.
         */
        const LIST_TEXT_SIZE = 150;

        /**
         * @var array
         */
        static protected $defaults = array (
            'COLS'         => 65,
            'ROWS'         => 5,
            'EDIT_IN_LIST' => false,
        );

        /**
         * TextAreaWidget constructor.
         *
         * @param Lang $lang
         */
        public function __construct ()
        {
            $this->lang = new Lang(get_class ());
        }

        /**
         * @inheritdoc
         */
        protected function getEditHtml ()
        {
            $cols = $this->getSettings ('COLS');
            $rows = $this->getSettings ('ROWS');

            return '<textarea cols="' .
            $cols .
            '" rows="' .
            $rows .
            '" name="' .
            $this->getEditInputName () .
            '">' .
            static::prepareToOutput ($this->getValue (), false) .
            '</textarea>';
        }

        /**
         * @inheritdoc
         */
        public function generateRow (&$row, $data)
        {
            $text = $this->getValue ();

            if ($this->getSettings ('EDIT_IN_LIST') AND !$this->getSettings ('READONLY'))
            {
                $row->AddInputField ($this->getCode (), array ('style' => 'width:90%'));
            } else
            {
                if (strlen ($text) > self::LIST_TEXT_SIZE && !$this->isExcelView ())
                {
                    $pos = false;
                    $pos = $pos === false ? stripos ($text, " ", self::LIST_TEXT_SIZE) : $pos;
                    $pos = $pos === false ? stripos ($text, "\n", self::LIST_TEXT_SIZE) : $pos;
                    $pos = $pos === false ? stripos ($text, "</", self::LIST_TEXT_SIZE) : $pos;
                    $pos = $pos === false ? 300 : $pos;
                    $text = substr ($text, 0, $pos) . " ...";
                }

                $text = static::prepareToOutput ($text);

                $row->AddViewField ($this->code, $text);
            }
        }
    }