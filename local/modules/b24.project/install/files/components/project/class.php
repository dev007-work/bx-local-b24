<?php

use Bitrix\Main\Loader;

defined('B_PROLOG_INCLUDED') || die();

class ProjectComponent extends CBitrixComponent
{
    public function executeComponent(): void
    {
        $root = $this->arParams['SEF_FOLDER'];
        $urlTemplates = $this->arParams['URL_TEMPLATES'];

        $page = CComponentEngine::parseComponentPath($root, $urlTemplates, $variables);

        $this->arResult = [
            'VARIABLES'        => $variables,
            'DETAIL_PAGE_URL'  => $root . $urlTemplates['detail'],
            'LIST_PAGE_URL'    => $root . $urlTemplates['list'],
            'HISTORY_PAGE_URL' => $root . $urlTemplates['history'],
        ];

        $this->includeComponentTemplate($page ?: $this->arParams['DEFAULT_PAGE']);
    }
}