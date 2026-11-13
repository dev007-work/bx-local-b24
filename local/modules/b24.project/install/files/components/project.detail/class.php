<?php

use B24\Project\Integration\UI\EntityEditor\ProjectProvider;
use B24\Project\Project;
use B24\Project\Service;
use B24\Project\ServiceException;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Loader;
use Bitrix\Main\ObjectException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Error;
use Bitrix\UI\Buttons\Button;
use Bitrix\UI\Buttons\Color;
use Bitrix\UI\Buttons\Icon;

defined('B_PROLOG_INCLUDED') || die();

class ProjectDetailComponent extends CBitrixComponent implements Controllerable
{
    private Service $projectService;

    public function __construct($component = null)
    {
        parent::__construct($component);

        Loader::requireModule('b24.project');

        $this->projectService = new Service();
    }

    public function executeComponent(): void
    {
        $project = $this->projectService->getById($this->arParams['PROJECT_ID']);
        $provider = new ProjectProvider($project);

        $this->arResult = [
            'form' => array_merge(
                $provider->getFields(),
                [
                    'COMPONENT_AJAX_DATA' => [
                        'COMPONENT_NAME'    => $this->getName(),
                        'SIGNED_PARAMETERS' => $this->getSignedParameters(),
                    ],
                    'ENABLE_CONFIG_CONTROL' => false,
                ],
            ),
            'toolbar' => [
                'buttons' => [
                    new Button([
                        'text'  => 'История',
                        'link'  => CComponentEngine::makePathFromTemplate(
                            $this->arParams['HISTORY_PAGE_URL'],
                            ['PROJECT_ID' => $project->id],
                        ),
                        'icon'  => Icon::LIST,
                        'color' => Color::LIGHT_BORDER,
                    ])
                ]
            ]
        ];

        $this->includeComponentTemplate();
    }

    public function saveAction(array $data): AjaxJson
    {
        $errorCollection = new ErrorCollection();

        if (!empty($data['ESTIMATED_COMPLETION_DATE'])) {
            try {
                $estimatedCompletionDate = new DateTime($data['ESTIMATED_COMPLETION_DATE']);
            } catch (ObjectException) {
                $errorCollection->setError(new Error('Некорректная планируемая дата окончания проекта'));
            }
        }

        if (!empty($data['COMPLETION_DATE'])) {
            try {
                $completionDate = new DateTime($data['COMPLETION_DATE']);
            } catch (ObjectException) {
                $errorCollection->setError(new Error('Некорректная дата окончания проекта'));
            }
        }

        if (!$errorCollection->isEmpty()) {
            return AjaxJson::createError($errorCollection);
        }

        try {
            if (!empty($this->arParams['PROJECT_ID'])) {
                // редактирование существующего проекта
                $item = $this->projectService->getById((int)$this->arParams['PROJECT_ID']);

                $item->estimatedCompletionDate = $estimatedCompletionDate ?? $item->estimatedCompletionDate;
                $item->completionDate = $completionDate ?? $item->completionDate;
                $item->title = $data['TITLE'] ?? $item->title;
                $item->description = $data['DESCRIPTION'] ?? $item->description;
                $item->comment = $data['COMMENT'] ?? $item->comment;
                $item->income = $data['INCOME'] ?? $item->income;
                $item->responsibleId = $data['RESPONSIBLE_ID'] ?? $item->responsibleId;

                $this->projectService->update($item);
            } else {
                // создание нового проекта
                $item = new Project(
                    $data['ID'] ?? null,
                    $data['TITLE'],
                    null,
                    null,
                    null,
                    null,
                    $estimatedCompletionDate ?? null,
                    $completionDate ?? null,
                    $data['DESCRIPTION'],
                    $data['RESPONSIBLE_ID'],
                    $data['COMMENT'],
                    $data['INCOME']
                );

                $item = $this->projectService->create($item);
            }

            return AjaxJson::createSuccess([
                'ENTITY_ID'    => $item->id,
                'REDIRECT_URL' => CComponentEngine::makePathFromTemplate(
                    $this->arParams['DETAIL_PAGE_URL'],
                    ['PROJECT_ID' => $item->id]
                )
            ]);
        } catch (ServiceException $e) {
            $errorCollection->setError(new Error('Не удалось сохранить проект'));

            return AjaxJson::createError($errorCollection);
        }
    }

    public function configureActions(): array
    {
        return [];
    }

    /**
     * Метод определяет, какие параметры arParams нужно передавать в Ajax обработчики
     *
     * @return string[]|null
     */
    protected function listKeysSignedParameters(): ?array
    {
        return ['PROJECT_ID', 'DETALI_PAGE_URL'];
    }
}