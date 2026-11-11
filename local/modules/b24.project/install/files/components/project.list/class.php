<?php

use B24\Project\Collection;
use B24\Project\Integration\UI\FieldNameProvider;
use B24\Project\Integration\UI\PageNavigationFactory;
use B24\Project\Integration\Intranet\Employee\Service as EmployeeService;
use B24\Project\Project;
use B24\Project\Service;
use Bitrix\Main\Grid\Options;

defined('B_PROLOG_INCLUDED') || die();

class ProjectListComponent extends CBitrixComponent
{
    const GRID_ID = 'investment-project-grid';

    private Service $projectService;
    private PageNavigationFactory $navigationFactory;
    private FieldNameProvider $fieldNameProvider;
    private EmployeeService $employeeService;

    public function __construct($component = null)
    {
        parent::__construct($component);

        $this->projectService = new Service();
        $this->navigationFactory = new PageNavigationFactory();
        $this->fieldNameProvider = new FieldNameProvider();
        $this->employeeService = new EmployeeService();
    }

    public function executeComponent(): void
    {
        // постраничная навигация
        $gridOptions = new Options(self::GRID_ID);
        $navParams = $gridOptions->GetNavParams();
        $navigation = $this->navigationFactory->create(
            $navParams['nPageSize'],
            $this->projectService->count(),
        );

        // сортировка
        $sort = $gridOptions->getSorting(['sort' => ['ID' => 'DESC']]);

        // выборка проектов
        $projects = $this->projectService->getFragment(
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
            'id'   => $p->id,
            'data' => $this->prepareRowData($p, $employees),
        ]);
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

    public function formatEmployee(array $employee): string
    {
        return sprintf('<a href="%s">%s</a>', $employee['PROFILE_URL'], $employee['FORMATTED_NAME']);
    }
}