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
     * <li> <b>INPUT_SIZE</b> - (int) значение атрибута size для input </li>
     * <li> <b>WINDOW_WIDTH</b> - (int) значение width для всплывающего окна выбора элемента </li>
     * <li> <b>WINDOW_HEIGHT</b> - (int) значение height для всплывающего окна выбора элемента </li>
     * </ul>
     *
     * @author Kirill Lahtachev <lahtachev@gmail.com>
     */
    class EmployeeWidget
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
         * EmployeeWidget constructor.
         *
         * @param array $settings
         */
        public function __construct (array $settings = array ())
        {
            $this->lang = new Lang(get_class ());
        }

        /**
         * {@inheritdoc}
         */
        public function getEditHtml ()
        {
            global $USER, $APPLICATION;

            $inputSize = (int) $this->getSettings ('INPUT_SIZE');
            $windowWidth = (int) $this->getSettings ('WINDOW_WIDTH');
            $windowHeight = (int) $this->getSettings ('WINDOW_HEIGHT');

            $name = 'FIELDS';

            $key = $this->getCode ();

            $elementId = $this->getValue ();

            $name_x = preg_replace ("/([^a-z0-9])/is", "_", $this->getEditInputName ());

            ob_start ();

            $val = intval ($elementId) > 0 ? intval ($elementId) : '';

            echo sprintf (
                '<input type="text" name="%s" id="%s" value="%s" size="3" class="typeinput"/>',
                $this->getEditInputName (),
                $name_x,
                $val
            );

            $APPLICATION->IncludeComponent (
                'bitrix:intranet.user.search',
                '',
                array (
                    'INPUT_NAME'  => $name_x,
                    'MULTIPLE'    => 'N',
                    'SHOW_BUTTON' => 'Y',
                ),
                null,
                array ('HIDE_ICONS' => 'Y')
            );

            echo sprintf (
                '<IFRAME style="width:0; height:0; border: 0; display: none;" src="javascript:void(0)" name="hiddenframe%s" ' .
                'id="hiddenframe%s"></IFRAME><span id="div_%s"></span>',
                htmlspecialcharsbx ($this->getEditInputName ()),
                $name_x,
                $name_x
            );

            echo sprintf (
                '<script>
                    var value_%s = \'\';
                    function Ch%s() {
                        var DV_%s = document.getElementById("div_%s");
                        if (document.getElementById(\'%s\')) {
                            var old_value = value_%s;
                            value_%s=parseInt(document.getElementById(\'%s\').value);
                            if (value_%s > 0) {
                                if (old_value != value_%s) {
                                    DV_%s.innerHTML = \'<i>%s</i>\';
                                    if (value_%s != %s) {
                                        console.log(1);
                                        document.getElementById("hiddenframe%s").src=\'/bitrix/admin/get_user.php?ID=\'+value_%s+\'&strName=%s&lang=%s\';
                                    } else {
                                        console.log(2);
                                        DV_%s.innerHTML = \'[<a title="%s" class="tablebodylink" href="/bitrix/admin/user_edit.php?ID=%s&lang=%s">%s</a>] (%s) %s %s\';
                                    }
                                }
                            } else {
                                DV_%s.innerHTML = \'\';
                            }
                        }
                        setTimeout(function(){Ch%s()},1000);
                    }
                    Ch%s();
                    </script>',
                $name_x,
                $name_x,
                $name_x,
                $name_x,
                $name_x,
                $name_x,
                $name_x,
                $name_x,
                $name_x,
                $name_x,
                $name_x,
                \CUtil::JSEscape (GetMessage ("MAIN_WAIT")),
                $name_x,
                intval ($USER->GetID ()),
                $name_x,
                $name_x,
                $name_x,
                LANG . (defined ("ADMIN_SECTION") && ADMIN_SECTION === true ? "&admin_section=Y" : ""),
                $name_x,
                \CUtil::JSEscape (GetMessage ("MAIN_EDIT_USER_PROFILE")),
                $USER->GetID (),
                LANG,
                $USER->GetID (),
                \CUtil::JSEscape (htmlspecialcharsbx ($USER->GetLogin ())),
                \CUtil::JSEscape (htmlspecialcharsbx ($USER->GetLastName ())),
                \CUtil::JSEscape (htmlspecialcharsbx ($USER->GetFirstName ())),
                $name_x,
                $name_x,
                $name_x
            );

            $return = ob_get_contents ();

            ob_end_clean ();

            return $return;
        }

        public function getValueReadonly ()
        {
            $userId = $this->getValue ();
            $htmlUser = '';

            if (!empty($userId) && $userId != 0)
            {
                $rsUser = UserTable::getById ($userId);
                $user = $rsUser->fetch ();

                $htmlUser = '[<a href="user_edit.php?lang=ru&ID=' . $user['ID'] . '">' . $user['ID'] . '</a>]';

                if ($user['EMAIL'])
                {
                    $htmlUser .= ' (' . $user['EMAIL'] . ')';
                }

                $htmlUser .= ' ' . static::prepareToOutput ($user['NAME']) . '&nbsp;' . static::prepareToOutput ($user['LAST_NAME']);
            }

            return $htmlUser;
        }

        /**
         * @inheritdoc
         */
        public function generateRow (&$row, $data)
        {
            $userId = $this->getValue ();
            $strUser = '';

            if (!empty($userId) && $userId != 0)
            {
                $rsUser = UserTable::getById ($userId);
                $user = $rsUser->fetch ();

                $strUser = '[<a href="user_edit.php?lang=ru&ID=' . $user['ID'] . '">' . $user['ID'] . '</a>]';

                if ($user['EMAIL'])
                {
                    $strUser .= ' (' . $user['EMAIL'] . ')';
                }

                $strUser .= ' ' . static::prepareToOutput ($user['NAME']) . '&nbsp;' . static::prepareToOutput ($user['LAST_NAME']);
            }

            if ($strUser)
            {
                $row->AddViewField ($this->getCode (), $strUser);
            } else
            {
                $row->AddViewField ($this->getCode (), '');
            }
        }
    }
