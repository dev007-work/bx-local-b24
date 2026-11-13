<?php

namespace B24\Project\Integration\UI\SidePanel;

use Bitrix\Main\Page\Asset;

class RuleInjector
{
    /**
     * Обработчик события эпилога.
     * Изменяет на детальной странице проекта правила для открытия сайдбара
     *
     * @return void
     */
    public static function injextAnchorRules(): void
    {
        $asset = Asset::getInstance();
        $asset->addString(
            $asset->insertJs(
                <<<JS
                    BX.ready(function () {
                        if (window.top !== window) {
                            return;
                        }
                        
                        BX.SidePanel.Instance.bindAnchors({
                            rules: [
                                {
                                    condition: [new RegExp('/invest/detail/[0-9]+/')],
                                    options: {
                                        width: 1200,
                                        cacheable: false,
                                        label: {
                                            text: 'Проект'
                                        }
                                    }
                                },
                                {
                                    condition: [new RegExp('/invest/history/[0-9]+/')],
                                    options: {
                                        width: 1200,
                                        cacheable: false,
                                        label: {
                                            text: 'История'
                                        }
                                    }
                                }
                            ]
                        });
                    });
                JS,
                inline: true
            ),
        );
    }
}