<?php

    namespace Lacodda\BxModule\Helper\Lang;

    /**
     * Class AdminEditHelper
     *
     * @package Lacodda\BxModule\Helper\Lang
     */
    class AdminEditHelper
    {
        /**
         * @var array
         */
        public $mess = [
            'DEFAULT_TAB'             => 'Элемент',
            'RETURN_TO_LIST'          => 'Список',
            'DELETE'                  => 'Удалить',
            'VALIDATION_ERROR'        => 'Не заполнены обязательные поля:\n#FIELD_LIST#',
            'VALIDATION_ERROR_FIELDS' => 'Не заполнены обязательные поля:',
            'NEW_ELEMENT'             => 'Новый элемент',
            'EDIT_TITLE'              => 'Элемент #ID#',
            'EDIT_DELETE_CONFIRM'     => 'Удалить запись?',
            'ACTIONS'                 => 'Действия',
            'ADD_ELEMENT'             => 'Добавить элемент',
            'DELETE_ELEMENT'          => 'Удалить элемент',
            'EDIT_DELETE_FORBIDDEN'   => 'Не хватает прав доступа для удаления элемента',
            'EDIT_WRITE_FORBIDDEN'    => 'Не хватает прав доступа для изменения элемента',
        ];
    }