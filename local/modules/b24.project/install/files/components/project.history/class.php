<?php

use B24\Project\Collection as ProjectCollection;
use B24\Project\History\Entry;
use B24\Project\History\Filter;
use B24\Project\Integration\UI\FieldNameProvider;
use B24\Project\Integration\UI\Filter\HistoryDataProvider;
use B24\Project\Integration\UI\Filter\HistorySettings;
use B24\Project\Integration\UI\Filter\ProjectDataProvider;
use B24\Project\Integration\UI\Filter\ProjectSettings;
use B24\Project\Integration\UI\PageNavigationFactory;
use B24\Project\Project;
use B24\Project\Service as ProjectService;
use B24\Project\History\Service as HistoryService;
use B24\Project\Integration\Intranet\Employee\Service as EmployeeService;
use Bitrix\Main\Filter\Field;
use Bitrix\Main\Filter\Filter as FilterService;
use Bitrix\Main\Grid\Options as GridService;
use Bitrix\Main\Loader;

defined('B_PROLOG_INCLUDED') || die();

Loader::requireModule('b24.project');

class ProjectHistoryComponent extends CBitrixComponent
{
    private const GRID_ID = 'project-history-grid';

    public function __construct(
        $component = null,
        private ProjectService $projectService = new ProjectService(),
        private HistoryService $historyService = new HistoryService(),
        private EmployeeService $employeeService = new EmployeeService(),
        private PageNavigationFactory $pageNavigationFactory = new PageNavigationFactory(),
        private FieldNameProvider $fieldNameProvider = new FieldNameProvider(),
    )
    {
        parent::__construct($component);
    }

    public function executeComponent()
    {
        $gridService = new GridService(self::GRID_ID);

        $historyDataProvider = new HistoryDataProvider(
            new ProjectDataProvider(new ProjectSettings($gridService->getId())),
            new HistorySettings($gridService->getId(), $this->fieldNameProvider)
        );

        $filterService = new FilterService(
            $gridService->getId(),
            $historyDataProvider
        );

        $fields = $filterService->getValue();
        $filter = new Filter(
            $this->arParams['PROJECT_ID'] ?? $fields['PROJECT_ID'],
            $fields['FIELD_NAME'] ?? null,
            $fields['PREVIOUS_VALUE'] ?? null,
            $fields['CURRENT_VALUE'] ?? null,
            $fields['CHANGED_AT'] ?? null,
            (array)$fields['AUTHOR_ID'] ?? null,
        );

        $count = $this->historyService->count($filter);
        $navigationParameters = $gridService->GetNavParams();
        $navigation = $this->pageNavigationFactory->create($navigationParameters['nPageSize'], $count);
        $sort = $gridService->getSorting(['sort' => ['CHANGED_AT' => 'DESC', 'ID' => 'DESC']]);

        $fragment = $this->historyService->getFragment(
            $filter,
            $sort['sort'],
            $navigation->getPageSize(),
            $navigation->getCurrentPage(),
        );

        $projectIds = $userIds = [];
        foreach ($fragment as $item) {
            $projectIds[] = $item->projectId;
            $userIds[] = $item->authorId;
        }

        $projects = $this->projectService->getByIds(...array_unique($projectIds));
        $employees = $this->employeeService->getByIds(...array_unique($userIds));

        $this->arResult = [
            'grid' => [
                'GRID_ID'             => $gridService->getId(),
                'COLUMNS'             => array_map(
                    static fn(Field $field): array => [
                        'id'      => $field->getId(),
                        'type'    => $field->getType(),
                        'name'    => $field->getName(),
                        'default' => $field->isDefault(),
                    ],
                    $filterService->getFields()
                ),
                'ROWS'                => $fragment->map(fn(Entry $history): array => [
                    'id'   => $history->id,
                    'data' => $this->prepareRowData(
                        $history,
                        $filterService->getFields(),
                        $projects,
                        $employees
                    ),
                ]),
                'NAV_OBJECT'          => $navigation,
                'TOTAL_ROWS_COUNT'    => $navigation->getRecordCount(),
                'PAGE_SIZES'          => $navigation->getPageSizes(),
                'SHOW_PAGESIZE'       => true,
                // ajax
                'AJAX_MODE'           => 'Y',
                'AJAX_OPTION_JUMP'    => 'N',
                'AJAX_OPTION_HISTORY' => 'N',
            ],
            'filter' => [
                'FILTER'         => $filterService->getFieldArrays(),
                'FILTER_ID'      => $filterService->getID(),
                'GRID_ID'        => $gridService->getId(),
                'ENABLE_LABEL'   => true,
                'DISABLE_SEARCH' => true,
            ],
        ];

        $this->includeComponentTemplate();
    }

    public function prepareRowData(
        Entry $item,
        array $fields,
        ProjectCollection $projectCollection,
        array $employees
    ): array
    {
        $row = [];
        foreach ($fields as $field) {
            $row[$field->getId()] = match ($field->getId()) {
                'ID' => $item->id,
                'PROJECT_ID' => $this->formatProject(
                    $projectCollection->get($item->projectId),
                    $this->arParams['DETAIL_PAGE_URL']
                ),
                'AUTHOR_ID' => $this->formatEmployee($employees[$item->authorId]),
                'FIELD_NAME' => $this->fieldNameProvider->getProjectFieldName($item->fieldName),
                'PREVIOUS_VALUE' => strlen($item->previousValue) > 0 ? $item->previousValue : '',
                'CURRENT_VALUE' => strlen($item->currentValue) > 0 ? $item->currentValue : '',
                'CHANGED_AT' => $item->changedAt,
            };
        }

        return $row;
    }

    public function formatEmployee(array $employee): string
    {
        return sprintf(
            '<a href="%s">%s</a>',
            $employee['PROFILE_URL'],
            $employee['FORMATTED_NAME']
        );
    }

    public function formatProject(Project $project, string $urlTemplate): string
    {
        return sprintf(
            '<a href="%s">%s</a>',
            CComponentEngine::makePathFromTemplate($urlTemplate, ['PROJECT_ID' => $project->id]),
            $project->title
        );
    }
}