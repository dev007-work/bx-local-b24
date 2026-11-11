<?php

namespace B24\Project\Integration\UI;

use Bitrix\Main\UI\PageNavigation;

class PageNavigationFactory
{
    public function create(int $size, int $count): PageNavigation
    {
        $pageNavigation = new PageNavigation('n');
        $pageNavigation->setPageSize($size);
        $pageNavigation->setRecordCount($count);
        $pageNavigation->setPageSizes([
            ['NAME' => '5', 'VALUE' => '5'],
            ['NAME' => '10', 'VALUE' => '10'],
            ['NAME' => '20', 'VALUE' => '20'],
            ['NAME' => '50', 'VALUE' => '50'],
            ['NAME' => '100', 'VALUE' => '100'],
        ]);
        $pageNavigation->initFromUri();

        return $pageNavigation;
    }
}