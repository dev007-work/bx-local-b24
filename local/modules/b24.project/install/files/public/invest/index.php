<?php

use Bitrix\Main\Loader;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/**
 * @global CMain $APPLICATION
 */

Loader::requireModule('b24.project');

$APPLICATION->IncludeComponent(
    'b24.project:project',
    '',
    [
        'SEF_FOLDER'    => '/invest/',
        'URL_TEMPLATES' => [
            'list'    => 'list/',
            'detail'  => 'detail/#PROJECT_ID#/',
            'history' => 'history/#PROJECT_ID#/',
        ],
        'DEFAULT_PAGE'  => 'list',
    ]
);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';