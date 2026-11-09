<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arActivityDescription = [
    'NAME'        => Loc::getMessage('PAUSE_UNTIL_CHANGE_ACTIVITY_NAME'),
    'DESCRIPTION' => Loc::getMessage('PAUSE_UNTIL_CHANGE_ACTIVITY_DESCRIPTION'),
    'JSCLASS'     => 'BizProcActivity',
    'TYPE'        => 'activity',
    'CLASS'       => 'PauseUntilChangeActivity',
    'CATEGORY'    => [
        'ID' => 'other'
    ],
    'FILTER'      => [
        'INCLUDE' => [
            ['lists']
        ]
    ],
    'RETURN'      => [
        'UpdatedFields' => [
            'NAME' => Loc::getMessage('PAUSE_UNTIL_CHANGE_ACTIVITY_RETURN_UPDATED_FIELDS'),
            'TYPE' => 'string'
        ],
        'IsTimeout'     => [
            'NAME' => Loc::getMessage('PAUSE_UNTIL_CHANGE_ACTIVITY_RETURN_IS_TIMEOUT'),
            'TYPE' => 'int'
        ]
    ]
];