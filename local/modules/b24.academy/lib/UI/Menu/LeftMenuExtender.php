<?php

namespace B24\Academy\UI\Menu;

use Bitrix\Main\Context;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\UI\Extension;

class LeftMenuExtender
{
    public static function handleOnEpilog(): void
    {
        if (Context::getCurrent()->getRequest()->isAdminSection()) {
            return;
        }

        Extension::load('b24.academy.menu.left');
        Asset::getInstance()->addString(
            <<<HTML
            <script >
            BX.ready(function () {
                if (document.getElementById('companyFactMenuItem')) {
                    return;
                }
                
                const footerItemsBox = document.querySelector('.menu-items-footer-inner');
                if (footerItemsBox === null) {
                    console.warn('No button container');
                    return;
                }
                
                const menuItem = document.createElement('div');
                menuItem.id = 'companyFactMenuItem';
                menuItem.innerText = 'Факты о компании';
                menuItem.onclick = BX.b24.academy.menu.left.showFacts;
                menuItem.classList.add('ui-btn');
                
                footerItemsBox.append(menuItem);
            });
            </script>
            HTML
        );
    }
}