<?php
    if (!@include_once $_SERVER["DOCUMENT_ROOT"] . "/vendor/lacodda/bxmodule/admin/route.php")
    {
        if (!@include_once $_SERVER["DOCUMENT_ROOT"] . "/local/vendor/lacodda/bxmodule/admin/route.php")
        {
            include $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/404.php';
        }
    }
