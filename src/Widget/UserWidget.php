<?php

    namespace Lacodda\BxModule\Widget;

    use Bitrix\Main\UserTable;
    use Lacodda\BxModule\Helper\Lang;

    /**
     * Виджет для вывода пользователя.
     *
     * Доступные опции:
     * <ul>
     * <li> STYLE - inline-стили
     * <li> SIZE - значение атрибута size для input
     * </ul>
     *
     * @author Kirill Lahtachev <lahtachev@gmail.com>
     */
    class UserWidget
        extends NumberWidget
    {
        /**
         * @var Lang
         */
        protected $lang;

        /**
         * UserWidget constructor.
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
        public function getEditHtml ()
        {
            $style = $this->getSettings ('STYLE');
            $size = $this->getSettings ('SIZE');

            $userId = $this->getValue ();

            $htmlUser = '';

            if (!empty($userId) && $userId != 0)
            {
                $rsUser = UserTable::getById ($userId);
                $user = $rsUser->fetch ();

                $htmlUser = '[<a href="user_edit.php?lang=ru&ID=' .
                    $user['ID'] .
                    '">' .
                    $user['ID'] .
                    '</a>] (' .
                    $user['EMAIL'] .
                    ') ' .
                    $user['NAME'] .
                    '&nbsp;' .
                    $user['LAST_NAME'];
            }

            return '<input type="text"
                       name="' . $this->getEditInputName () . '"
                       value="' . static::prepareToTagAttr ($this->getValue ()) . '"
                       size="' . $size . '"
                       style="' . $style . '"/>' . $htmlUser;
        }

        /**
         * @inheritdoc
         */
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