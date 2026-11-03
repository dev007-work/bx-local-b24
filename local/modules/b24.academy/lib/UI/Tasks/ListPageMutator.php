<?php

namespace B24\Academy\UI\Tasks;

use Bitrix\Main\Page\Asset;
use CComponentEngine;

class ListPageMutator
{
    private const LIST_PAGE_TEMPLATES = [
        'personal' => 'company/personal/user/#user_id#/tasks/',
        'group'    => 'workgroups/group/#group_id#/tasks/',
    ];

    /**
     * Обработчик подключает файл стилей на нужных страницах.
     * Является ли текущая страница нужной определяется по шаблону из константы LIST_PAGE_TEMPLATES.
     *
     * @return void
     */
    public static function handleOnEpilog(): void
    {
        $page = CComponentEngine::parseComponentPath('/', ListPageMutator::LIST_PAGE_TEMPLATES, $vars);
        if ($page === false) {
            return;
        }
        Asset::getInstance()->addCss('/local/css/b24.academy/tasks/list.css');
    }
}