<?php

    namespace Lacodda\BxModule\Widget;

    use Bitrix\Iblock\SectionTable;
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
    class IblockSectionWidget
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
         * IblockSectionWidget constructor.
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

            $sectionId = $this->getValue ();

            if (!empty($sectionId))
            {
                $rsSection = SectionTable::getById ($sectionId);

                if (!$section = $rsSection->fetchAll ())
                {
                    $section['NAME'] = $this->lang->getMessage ('IBLOCK_SECTION_NOT_FOUND');
                }
            } else
            {
                $sectionId = '';
            }

            return '<input name="' .
            $this->getEditInputName () .
            '"
                     id="' .
            $name .
            '[' .
            $key .
            ']"
                     value="' .
            $sectionId .
            '"
                     size="' .
            $inputSize .
            '"
                     type="text">' .
            '<input type="button"
                    value="..."
                    onClick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_section_search.php?lang=' .
            LANGUAGE_ID .
            '&amp;IBLOCK_ID=' .
            $iblockId .
            '&amp;n=' .
            $name .
            '&amp;k=' .
            $key .
            '\', ' .
            $windowWidth .
            ', ' .
            $windowHeight .
            ');">' .
            '&nbsp;<span id="sp_' .
            md5 ($name) .
            '_' .
            $key .
            '" >' .
            static::prepareToOutput ($section['NAME']) .
            '</span>';
        }

        /**
         * {@inheritdoc}
         */
        public function getValueReadonly ()
        {
            $sectionId = $this->getValue ();

            if (!empty($sectionId))
            {
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

                return '<a href="/bitrix/admin/iblock_section_edit.php?IBLOCK_ID=' .
                $section['IBLOCK_ID'] .
                '&type=' .
                $section['IBLOCK_SECTION_IBLOCK_IBLOCK_TYPE_ID'] .
                '&ID=' .
                $sectionId .
                '&lang=ru">[' .
                $sectionId .
                '] ' .
                static::prepareToOutput ($section['NAME']) .
                '</a>';
            }
        }

        /**
         * {@inheritdoc}
         */
        public function generateRow (&$row, $data)
        {
            $sectionId = $this->getValue ();

            if (!empty($sectionId))
            {
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

                $html = '<a href="/bitrix/admin/iblock_section_edit.php?IBLOCK_ID=' .
                    $section['IBLOCK_ID'] .
                    '&type=' .
                    $section['IBLOCK_SECTION_IBLOCK_IBLOCK_TYPE_ID'] .
                    '&ID=' .
                    $sectionId .
                    '&lang=ru">[' .
                    $sectionId .
                    '] ' .
                    static::prepareToOutput ($section['NAME']) .
                    '</a>';
            } else
            {
                $html = '';
            }

            $row->AddViewField ($this->getCode (), $html);
        }
    }
