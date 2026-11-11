<?php
defined('B_PROLOG_INCLUDED') || die();

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */

$APPLICATION->IncludeComponent(
    'b24.project:project.list',
    '',
    []
);