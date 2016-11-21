<?php

    namespace Lacodda\BxModule\Widget;

    use Bitrix\Iblock\ElementTable;
    use Bitrix\Main\Loader;

    use Lacodda\BxModule\Helper\Lang;

    /**
     * Виджет для выбора элемента инфоблока.
     *
     * Доступные опции:
     * <ul>
     * <li> <b>IBLOCK_ID</b> - (int) ID инфоблока
     * <li> <b>INPUT_SIZE</b> - (int) значение атрибута size для input </li>
     * <li> <b>WINDOW_WIDTH</b> - (int) значение width для всплывающего окна выбора элемента </li>
     * <li> <b>WINDOW_HEIGHT</b> - (int) значение height для всплывающего окна выбора элемента </li>
     * </ul>
     *
     * @author Kirill Lahtachev <lahtachev@gmail.com>
     */
    class IblockElementWidget
        extends NumberWidget
    {
        /**
         * @var Lang
         */
        protected $lang;

        /**
         * @var array
         */
        static protected $defaults = array (
            'FILTER'        => '=',
            'INPUT_SIZE'    => 5,
            'WINDOW_WIDTH'  => 600,
            'WINDOW_HEIGHT' => 500,
        );

        /**
         * IblockElementWidget constructor.
         *
         * @param array $settings
         */
        public function __construct (array $settings = array ())
        {
            Loader::includeModule ('iblock');

            parent::__construct ($settings);

            $this->lang = new Lang(get_class ());
        }

        /**
         * {@inheritdoc}
         */
        public function getEditHtml ()
        {
            $iblockId = (int) $this->getSettings ('IBLOCK_ID');
            $inputSize = (int) $this->getSettings ('INPUT_SIZE');
            $windowWidth = (int) $this->getSettings ('WINDOW_WIDTH');
            $windowHeight = (int) $this->getSettings ('WINDOW_HEIGHT');

            $name = 'FIELDS';
            $key = $this->getCode ();

            $elementId = $this->getValue ();

            if (!empty($elementId))
            {
                $rsElement = ElementTable::getById ($elementId);

                if (!$element = $rsElement->fetchAll ())
                {
                    $element['NAME'] = $this->lang->getMessage ('IBLOCK_ELEMENT_NOT_FOUND');
                }
            } else
            {
                $elementId = '';
            }

            return sprintf (
                '<input name="%s" id="%s[%s]" value="%s" size="%s" type="text"><input type="button" value="..." onClick="jsUtils.OpenWindow' .
                '(\'/bitrix/admin/iblock_element_search.php?lang=%s&amp;IBLOCK_ID=%s&amp;n=%s&amp;k=%s\', %s, %s);">&nbsp;' .
                '<span id="sp_%s_%s" >%s</span>',
                $this->getEditInputName (),
                $name,
                $key,
                $elementId,
                $inputSize,
                LANGUAGE_ID,
                $iblockId,
                $name,
                $key,
                $windowWidth,
                $windowHeight,
                md5 ($name),
                $key,
                static::prepareToOutput ($element['NAME'])
            );
        }

        /**
         * {@inheritdoc}
         */
        public function getValueReadonly ()
        {
            $elementId = $this->getValue ();

            if (!empty($elementId))
            {
                $rsElement = ElementTable::getList (
                    [
                        'filter' => [
                            'ID' => $elementId,
                        ],
                        'select' => [
                            'ID',
                            'NAME',
                            'IBLOCK_ID',
                            'IBLOCK.IBLOCK_TYPE_ID',
                        ],
                    ]
                );

                $element = $rsElement->fetch ();

                return sprintf (
                    '<a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=%s&type=%s&ID=%s&lang=ru">[%s] %s</a>',
                    $element['IBLOCK_ID'],
                    $element['IBLOCK_ELEMENT_IBLOCK_IBLOCK_TYPE_ID'],
                    $elementId,
                    $elementId,
                    static::prepareToOutput ($element['NAME'])
                );
            }
        }

        /**
         * {@inheritdoc}
         */
        public function generateRow (&$row, $data)
        {
            $elementId = $this->getValue ();

            if (!empty($elementId))
            {
                $rsElement = ElementTable::getList (
                    [
                        'filter' => [
                            'ID' => $elementId,
                        ],
                        'select' => [
                            'ID',
                            'NAME',
                            'IBLOCK_ID',
                            'IBLOCK.IBLOCK_TYPE_ID',
                        ],
                    ]
                );

                $element = $rsElement->fetch ();

                $html = sprintf (
                    '<a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=%s&type=%s&ID=%s&lang=ru">[%s] %s</a>',
                    $element['IBLOCK_ID'],
                    $element['IBLOCK_ELEMENT_IBLOCK_IBLOCK_TYPE_ID'],
                    $elementId,
                    $elementId,
                    static::prepareToOutput ($element['NAME'])
                );
            } else
            {
                $html = '';
            }

            $row->AddViewField ($this->getCode (), $html);
        }
    }
