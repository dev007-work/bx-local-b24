<?php

namespace B24\Project\Integration\UI\EntityEditor;

use B24\Project\Integration\Intranet\Employee\Service;
use B24\Project\Integration\UI\FieldNameProvider;
use B24\Project\Project;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\UI\EntityEditor\BaseProvider;

class ProjectProvider extends BaseProvider
{
    private readonly FieldNameProvider $fieldNameProvider;
    private readonly Service $employeeService;

    public function __construct(
        private readonly ?Project $project,
    )
    {
        $this->fieldNameProvider = new FieldNameProvider();
        $this->employeeService = new Service();
    }


    public function getGUID(): string
    {
        return 'INVESTMENT_PROJECT';
    }

    public function getEntityId(): ?int
    {
        return $this->project?->id;
    }

    public function getEntityTypeName(): string
    {
        return 'investment_project';
    }

    /**
     * Подготавливает инфомрацию о всех полях, доступных в карточке детального просмотра проекта.
     * За структуру отображения полей (в каких разделах карточки какие поля находятся) отвечает {@link ProjectProvider::getEntityConfig()}
     *
     * @return array[]
     */
    public function getEntityFields(): array
    {
        $exists = isset($this->item);
        $userProfilePathTemplate = $this->employeeService->getProfileUrlTemplate();

        return [
            [
                'name'     => 'ID',
                'title'    => $this->fieldNameProvider->getProjectFieldName('ID'),
                'editable' => false,
                'type'     => 'hidden',
            ], [
                'name'     => 'RESPONSIBLE_ID',
                'title'    => $this->fieldNameProvider->getProjectFieldName('RESPONSIBLE_ID'),
                'editable' => true,
                'multiple' => false,
                'type'     => 'user',
                'data'     => [
                    'enableEditInView' => true,
                    'formated'         => 'RESPONSIBLE_FORMATTED_NAME',
                    'position'         => 'RESPONSIBLE_WORK_POSITION',
                    'photoUrl'         => 'RESPONSIBLE_PHOTO_URL',
                    'showUrl'          => 'PATH_TO_RESPONSIBLE_USER',
                    'pathToProfile'    => $userProfilePathTemplate
                ],
            ], [
                'name'     => 'TITLE',
                'title'    => $this->fieldNameProvider->getProjectFieldName('TITLE'),
                'editable' => true,
                'type'     => 'text',
            ], [
                'name'     => 'DESCRIPTION',
                'title'    => $this->fieldNameProvider->getProjectFieldName('DESCRIPTION'),
                'editable' => true,
                'type'     => 'textarea',
            ], [
                'name'     => 'COMMENT',
                'title'    => $this->fieldNameProvider->getProjectFieldName('COMMENT'),
                'editable' => true,
                'type'     => 'textarea',
            ], [
                'name'     => 'CREATED_AT',
                'title'    => $this->fieldNameProvider->getProjectFieldName('CREATED_AT'),
                'editable' => false,
                'type'     => $exists ? 'datetime' : 'hidden',
            ], [
                'name'     => 'CREATED_BY',
                'title'    => $this->fieldNameProvider->getProjectFieldName('CREATED_BY'),
                'editable' => false,
                'type'     => $exists ? 'user' : 'hidden',
                'data'     => [
                    'enableEditInView' => false,
                    'formated'         => 'CREATED_BY_FORMATTED_NAME',
                    'position'         => 'CREATED_BY_WORK_POSITION',
                    'photoUrl'         => 'CREATED_BY_PHOTO_URL',
                    'showUrl'          => 'PATH_TO_CREATED_BY_USER',
                    'pathToProfile'    => $userProfilePathTemplate
                ],
            ], [
                'name'     => 'UPDATED_AT',
                'title'    => $this->fieldNameProvider->getProjectFieldName('UPDATED_AT'),
                'editable' => false,
                'type'     => $exists ? 'datetime' : 'hidden',
            ], [
                'name'     => 'UPDATED_BY',
                'title'    => $this->fieldNameProvider->getProjectFieldName('UPDATED_BY'),
                'editable' => false,
                'type'     => $exists ? 'user' : 'hidden',
                'data'     => [
                    'enableEditInView' => false,
                    'formated'         => 'UPDATED_BY_FORMATTED_NAME',
                    'position'         => 'UPDATED_BY_WORK_POSITION',
                    'photoUrl'         => 'UPDATED_BY_PHOTO_URL',
                    'showUrl'          => 'PATH_TO_UPDATED_BY_USER',
                    'pathToProfile'    => $userProfilePathTemplate
                ],
            ], [
                'name'     => 'COMPLETION_DATE',
                'title'    => $this->fieldNameProvider->getProjectFieldName('COMPLETION_DATE'),
                'editable' => true,
                'type'     => 'datetime',
            ], [
                'name'     => 'ESTIMATED_COMPLETION_DATE',
                'title'    => $this->fieldNameProvider->getProjectFieldName('ESTIMATED_COMPLETION_DATE'),
                'editable' => true,
                'type'     => 'datetime',
            ], [
                'name'     => 'INCOME',
                'title'    => $this->fieldNameProvider->getProjectFieldName('INCOME'),
                'editable' => true,
                'type'     => 'text',
            ],
        ];
    }

    /**
     * Добавляет системные параметры проекта только для существующих проектов.
     *
     * @return array[]
     */
    public function getEntityConfig(): array
    {
        $config = [
            [
                'type'     => 'column',
                'name'     => 'default_column',
                'elements' => [
                    [
                        'name'     => 'main',
                        'title'    => 'О проекте',
                        'type'     => 'section',
                        'elements' => [
                            ['name' => 'ID'],
                            ['name' => 'RESPONSIBLE_ID'],
                            ['name' => 'TITLE'],
                            ['name' => 'DESCRIPTION'],
                            ['name' => 'COMMENT'],
                            ['name' => 'COMPLETION_DATE'],
                            ['name' => 'ESTIMATED_COMPLETION_DATE'],
                            ['name' => 'INCOME'],
                        ]
                    ]
                ]
            ]
        ];

        if (isset($this->item)) {
            $config[] = [
                'name'     => 'additional',
                'title'    => 'Системная информация',
                'type'     => 'section',
                'editable' => false,
                'elements' => [
                    ['name' => 'CREATED_AT'],
                    ['name' => 'CREATED_BY'],
                    ['name' => 'UPDATED_AT'],
                    ['name' => 'UPDATED_BY'],
                ],
                'data'     => [
                    'enableToggling' => false
                ]
            ];
        }

        return $config;
    }

    /**
     * Возвращает информацию о проекте. Для проекта в режиме создания возвращает предзаполненные поля.
     * Предзаполнить можно только ответственного по проекту, это текущий пользователь.
     */
    public function getEntityData(): array
    {
        if (!isset($this->project)) {
            $responsibleId = CurrentUser::get()->getId();
            $responsible = $this->employeeService->getByIds($responsibleId);
            $responsible = reset($responsible);

            return array_merge(
                ['RESPONSIBLE_ID' => $responsibleId],
                $this->prepareEmployeeData($responsible, 'RESPONSIBLE')
            );
        }

        $employees = $this->employeeService->getByIds(
            $this->project->responsibleId,
            $this->project->createdBy,
            $this->project->updatedBy,
        );

        return array_merge(
            [
                'ID'                        => $this->project->id,
                'RESPONSIBLE_ID'            => $this->project->responsibleId,
                'TITLE'                     => $this->project->title,
                'DESCRIPTION'               => $this->project->description,
                'COMMENT'                   => $this->project->comment,
                'CREATED_AT'                => $this->project->createdAt->toString(),
                'CREATED_BY'                => $this->project->createdBy,
                'UPDATED_AT'                => $this->project->updatedAt->toString(),
                'UPDATED_BY'                => $this->project->updatedBy,
                'COMPLETION_DATE'           => $this->project->completionDate?->toString(),
                'ESTIMATED_COMPLETION_DATE' => $this->project->estimatedCompletionDate?->toString(),
                'INCOME'                    => $this->project->income
            ],
            $this->prepareEmployeeData($employees[$this->project->responsibleId], 'RESPONSIBLE'),
            $this->prepareEmployeeData($employees[$this->project->createdBy], 'CREATED_BY'),
            $this->prepareEmployeeData($employees[$this->project->updatedBy], 'UPDATED_BY'),
        );
    }

    /**
     * Подготавливает дополнительную системную информацию для поля типа "user" в детальной карточке проекта.
     */
    private function prepareEmployeeData(array $employee, string $fieldName): array
    {
        return [
            "{$fieldName}_FORMATTED_NAME" => $employee['FORMATTED_NAME'],
            "{$fieldName}_WORK_POSITION"  => $employee['WORK_POSITION'],
            "{$fieldName}_PHOTO_URL"      => $employee['PERSONAL_PHOTO_PATH'],
            "PATH_TO_{$fieldName}_USER"   => $employee['PROFILE_URL'],
        ];
    }
}