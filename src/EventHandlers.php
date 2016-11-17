<?php

    namespace Lacodda\BxModule;

    use Bitrix\Main\Context;
    use Bitrix\Main\Loader;
    use Lacodda\BxModule\Helper\Lang;

    /**
     * Перехватчики событий.
     *
     * Для каждого события, возникающего в системе, которе необходимо отлавливать «Админ-хелпером», создаётся
     * в данном классе одноимённый метод. Метод должен быть зарегистрирован в системе через установщик модуля.
     *
     * @author Kirill Lahtachev <lahtachev@gmail.com>
     */
    class EventHandlers
    {
        /**
         * @var Lang
         */
        protected $lang;

        /**
         * EventHandlers constructor.
         *
         * @param Lang $lang
         */
        public function __construct ()
        {
            $this->lang = new Lang(get_class ());
        }

        /**
         * Автоматическое подключение модуля в админке.
         *
         * Таки образом, исключаем необходимость прописывать в генераторах админки своих модулей
         * подключение «Админ-хелпера».
         *
         * @throws \Bitrix\Main\LoaderException
         */
        public static function onPageStart ()
        {
            if (Context::getCurrent ()->getRequest ()->isAdminSection ())
            {
                //
            }
        }
    }