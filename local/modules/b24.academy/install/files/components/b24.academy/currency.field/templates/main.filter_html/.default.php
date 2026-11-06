<?php

defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\Web\Json;

/**
 * @var array $arResult
 * @var CurrencyFieldComponent $component
 */

Extension::load('ui.entity-selector');

$jsonParameters = [
    'items' => $arResult['items'],
    'selectedItems' => $arResult['selectedItems'],
    'inputName' => $arResult['fieldName'],
    'isMultiple' => $component->isMultiple(),
];

$messages = [
        'CURRENCY_ENTITY_SELECTOR_TAB_NAME' => Loc::getMessage('B24_ACADEMY.UFTYPE_CURRENCY_FIELD_DESCRIPTION')
];
ob_start();
?>
    <script>
        BX.ready(() => {
            BX.Loc.setMessage(<?= Json::encode($messages); ?>);

            BX.Event.EventEmitter.subscribe('BX.Filter.Field:init', (/** @param {BaseEvent} */ event) => {
                const {id, node} = event.getData().field;
                const {inputName, items, selectedItems, isMultiple} = <?= Json::encode($jsonParameters); ?>;

                if (id !== inputName) {
                    return;
                }

                const tagSelector = new BX.UI.EntitySelector.TagSelector({
                    textBoxAutoHide: true,
                    showAvatars: false,
                    maxHeight: 99,
                    showCreateButton: false,
                    multiple: isMultiple,
                    cacheable: false,
                    dialogOptions: {
                        entities: [
                            {
                                id: 'currency'
                            }
                        ],
                        tabs: [
                            {
                                id: 'main',
                                title: BX.Loc.getMessage('CURRENCY_ENTITY_SELECTOR_TAB_NAME'),
                                visible: true
                            }
                        ],
                        items: items,
                        selectedItems: selectedItems
                    },
                    events: {
                        onTagAdd: (event) => {
                            const {tag} = event.getData();

                            if (!isMultiple) {
                                const input = document.querySelector(`input[name="${inputName}"]`);
                                input.value = tag.id;
                            } else {
                                const input = BX.Dom.create({
                                    tag: 'input',
                                    attrs: {
                                        name: `${inputName}[${tag.id}]`,
                                        value: tag.id,
                                        type: 'hidden'
                                    }
                                });
                                BX.Dom.adjust(
                                    node,
                                    {
                                        children: [input]
                                    }
                                )
                            }
                        },
                        onTagRemove: (event) => {
                            const {tag} = event.getData();

                            if (!isMultiple) {
                                const input = document.querySelector(`[name="${inputName}"]`);
                                input.value = '';
                            } else {
                                const input = document.querySelector(`[name="${inputName}[${tag.id}]"]`);
                                if (!input) {
                                    return;
                                }
                                BX.Dom.remove(input);
                            }
                        }
                    }
                });

                const fieldNode = node.querySelector(`[data-name="${inputName}"]`);

                tagSelector.renderTo(null);
                BX.Dom.insertBefore(tagSelector.getOuterContainer(), fieldNode);
                BX.Dom.insertBefore(
                    BX.Dom.create({
                        tag: 'input',
                        attrs: {
                            name: `${inputName}`,
                            type: 'hidden'
                        }
                    }),
                    fieldNode
                );
                BX.Dom.remove(fieldNode);
            });
        });
    </script>
<?php
Asset::getInstance()->addString(ob_get_clean());
