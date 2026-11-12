<?php

use B24\Project\Collection;
use B24\Project\Filter;
use B24\Project\Integration\UI\FieldNameProvider;
use B24\Project\Integration\UI\Filter\ProjectDataProvider;
use B24\Project\Integration\UI\Filter\ProjectSettings;
use B24\Project\Integration\UI\PageNavigationFactory;
use B24\Project\Integration\Intranet\Employee\Service as EmployeeService;
use B24\Project\Project;
use B24\Project\Service;
use B24\Project\ServiceException;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Error;
use Bitrix\Main\Grid\Options;
use Bitrix\Main\Loader;
use Bitrix\UI\Buttons\CreateButton;

defined('B_PROLOG_INCLUDED') || die();

class ProjectListComponent extends CBitrixComponent implements Controllerable
{
    const GRID_ID = 'investment-project-grid';

    private Service $projectService;
    private PageNavigationFactory $navigationFactory;
    private FieldNameProvider $fieldNameProvider;
    private EmployeeService $employeeService;

    public function __construct($component = null)
    {
        parent::__construct($component);

        Loader::requireModule('b24.project');

        $this->projectService = new Service();
        $this->navigationFactory = new PageNavigationFactory();
        $this->fieldNameProvider = new FieldNameProvider();
        $this->employeeService = new EmployeeService();
    }

    public function executeComponent(): void
    {
        $projectDataProvider = new ProjectDataProvider(
            new ProjectSettings(self::GRID_ID)
        );

        $gridOptions = new Options(self::GRID_ID);
        $filterService = new Bitrix\Main\Filter\Filter(
            $projectDataProvider->getID(),
            $projectDataProvider
        );

        // данные полей в фильтре
        $fields = $filterService->getValue();
        $filter = new Filter(
            $fields['TITLE'],
            $fields['CREATED_AT'],
            $fields['RESPONSIBLE_ID'],
            $fields['ESTIMATED_COMPLETION_DATE'],
            $fields['COMPLETION_DATE'],
        );

        // постраничная навигация
        $navParams = $gridOptions->GetNavParams();
        $navigation = $this->navigationFactory->create(
            $navParams['nPageSize'],
            $this->projectService->count($filter),
        );

        // сортировка
        $sort = $gridOptions->getSorting(['sort' => ['ID' => 'DESC']]);

        // выборка проектов
        $projects = $this->projectService->getFragment(
            $filter,
            $sort['sort'],
            $navigation->getPageSize(),
            $navigation->getCurrentPage(),
        );

        // выборка данных пользователей
        $userIds = [];
        foreach ($projects as $project) {
            $userIds[] = $project->responsibleId;
            $userIds[] = $project->createdBy;
            $userIds[] = $project->updatedBy;
        }
        $employees = $this->employeeService->getByIds(...array_unique($userIds));

        // формирование параметров компонента таблицы
        $this->arResult = [
            'grid' => [
                'GRID_ID'             => self::GRID_ID,
                'COLUMNS'             => $this->prepareColumns(),
                'ROWS'                => $this->prepareRows($projects, $employees),
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
                'FILTER_ID'      => self::GRID_ID,
                'GRID_ID'        => self::GRID_ID,
                'ENABLE_LABEL'   => true,
                'DISABLE_SEARCH' => true,
            ],
            'toolbar' => [
                'buttons' => [
                    new CreateButton([
                        'link' => CComponentEngine::makePathFromTemplate(
                            $this->arParams['DETAIL_PAGE_URL'],
                            ['PROJECT_ID' => 0],
                        )
                    ])
                ]
            ],
        ];

        $this->includeComponentTemplate();
    }

    public function prepareColumns(): array
    {
        return [
            'ID' => [
                'id'      => 'ID',
                'sort'    => 'ID',
                'name'    => $this->fieldNameProvider->getProjectFieldName('ID'),
                'default' => true,
            ],
            'TITLE' => [
                'id'      => 'TITLE',
                'sort'    => 'TITLE',
                'name'    => $this->fieldNameProvider->getProjectFieldName('TITLE'),
                'default' => true,
            ],
            'CREATED_AT' => [
                'id'      => 'CREATED_AT',
                'sort'    => 'CREATED_AT',
                'name'    => $this->fieldNameProvider->getProjectFieldName('CREATED_AT'),
                'default' => true,
            ],
            'CREATED_BY' => [
                'id'      => 'CREATED_BY',
                'name'    => $this->fieldNameProvider->getProjectFieldName('CREATED_BY'),
                'default' => true,
            ],
            'UPDATED_AT' => [
                'id'      => 'UPDATED_AT',
                'sort'    => 'UPDATED_AT',
                'name'    => $this->fieldNameProvider->getProjectFieldName('UPDATED_AT'),
                'default' => true,
            ],
            'UPDATED_BY' => [
                'id'      => 'UPDATED_BY',
                'name'    => $this->fieldNameProvider->getProjectFieldName('UPDATED_BY'),
                'default' => true,
            ],
            'ESTIMATED_COMPLETION_DATE' => [
                'id'      => 'ESTIMATED_COMPLETION_DATE',
                'sort'    => 'ESTIMATED_COMPLETION_DATE',
                'name'    => $this->fieldNameProvider->getProjectFieldName('ESTIMATED_COMPLETION_DATE'),
                'default' => true,
            ],
            'COMPLETION_DATE' => [
                'id'      => 'COMPLETION_DATE',
                'sort'    => 'COMPLETION_DATE',
                'name'    => $this->fieldNameProvider->getProjectFieldName('COMPLETION_DATE'),
                'default' => true,
            ],
            'DESCRIPTION' => [
                'id'      => 'DESCRIPTION',
                'sort'    => 'DESCRIPTION',
                'name'    => $this->fieldNameProvider->getProjectFieldName('DESCRIPTION'),
                'default' => true,
            ],
            'RESPONSIBLE_ID' => [
                'id'      => 'RESPONSIBLE_ID',
                'name'    => $this->fieldNameProvider->getProjectFieldName('RESPONSIBLE_ID'),
                'default' => true,
            ],
            'COMMENT' => [
                'id'      => 'COMMENT',
                'name'    => $this->fieldNameProvider->getProjectFieldName('COMMENT'),
                'default' => true,
            ],
            'INCOME' => [
                'id'      => 'INCOME',
                'sort'    => 'INCOME',
                'name'    => $this->fieldNameProvider->getProjectFieldName('INCOME'),
                'default' => true,
            ],
        ];
    }

    public function prepareRows(Collection $projects, array $employees): array
    {
        return $projects->map(fn(Project $p): array => [
            'id'      => $p->id,
            'actions' => $this->prepareRowActions($p),
            'data'    => $this->prepareRowData($p, $employees),
        ]);
    }

    public function prepareRowActions(Project $project): array
    {
        $gridId = self::GRID_ID;

        return [
            [
                'text'    => 'Открыть проект',
                'default' => false,
                'href'    => CComponentEngine::makePathFromTemplate(
                    $this->arParams['DETAIL_PAGE_URL'],
                    ['PROJECT_ID' => $project->id],
                )
            ], [
                'text'    => 'Удалить',
                'default' => false,
                'onclick' => "BX.B24.Project.Grid.Manager.deleteProject('$gridId', $project->id);",
            ],
        ];
    }

    public function prepareRowData(Project $project, array $employees): array
    {
        return [
            'ID'                        => $project->id,
            'RESPONSIBLE_ID'            => $this->formatEmployee($employees[$project->responsibleId]),
            'TITLE'                     => $project->title,
            'DESCRIPTION'               => $project->description,
            'COMMENT'                   => $project->comment,
            'CREATED_AT'                => $project->createdAt,
            'CREATED_BY'                => $this->formatEmployee($employees[$project->createdBy]),
            'UPDATED_AT'                => $project->updatedAt,
            'UPDATED_BY'                => $this->formatEmployee($employees[$project->updatedBy]),
            'COMPLETION_DATE'           => $project->completionDate,
            'ESTIMATED_COMPLETION_DATE' => $project->estimatedCompletionDate,
            'INCOME'                    => $project->income,
        ];
    }

    public function deleteProjectAction(int $projectId): AjaxJson
    {
        $errorCollection = new ErrorCollection();
        $result = [];

        try {
            $project = $this->projectService->getById($projectId);
            $this->projectService->delete($project);
        } catch (ServiceException $e) {
            $errorCollection->add([
                new Error('не удалось удалить проект.'),
                Error::createFromThrowable($e)
            ]);
        }

        return new AjaxJson(
            $result,
            $errorCollection->isEmpty() ? AjaxJson::STATUS_SUCCESS : AjaxJson::STATUS_ERROR,
            $errorCollection
        );
    }

    public function formatEmployee(array $employee): string
    {
        return sprintf('<a href="%s">%s</a>', $employee['PROFILE_URL'], $employee['FORMATTED_NAME']);
    }

    public function configureActions(): array
    {
        return [];
    }
}