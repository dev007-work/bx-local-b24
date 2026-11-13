<?php

namespace B24\Project\Integration\Rest;

use B24\Project\DateRange;
use B24\Project\Filter;
use B24\Project\Service as ProjectService;
use B24\Project\ServiceException;
use Bitrix\Rest\RestException;
use CRestServer;
use CRestUtil;
use IRestService;

class Service extends IRestService
{
    const SCOPE = 'b24.project';

    public function __construct(
        private readonly ProjectService $projectService,
    )
    {}

    /**
     * Метод-обработчик для регистрации методов REST
     *
     * @return array[]
     */
    public static function onRestServiceBuildDescription(): array
    {
        $service = new Service(new ProjectService());

        return [
            self::SCOPE => [
                'project.list' => [
                    'callback' => $service->getList(...),
                ],
                'project.get'  => [
                    'callback' => $service->get(...),
                ],
            ]
        ];
    }

    /**
     * Метод получения конкретного проекта
     *
     * @param array $parameters
     * @param $navigation
     * @param CRestServer $server
     * @return array
     * @throws RestException
     */
    public function get(array $parameters, $navigation, CRestServer $server): array
    {
        if (!isset($parameters['ID'])) {
            throw new RestException('Argument ID is not defined', RestException::ERROR_ARGUMENT);
        }

        $projectId = (int) $parameters['ID'];
        if ($projectId <= 0) {
            throw new RestException('Argument ID must be a positive number', RestException::ERROR_ARGUMENT);
        }

        $project = $this->projectService->getById($projectId);
        if ($project === null) {
            throw new RestException('Project not found', RestException::ERROR_NOT_FOUND);
        }

        return $this->convertProject($project);
    }

    /**
     * Метод получения списка проектов
     *
     * @param array $parameters
     * @param $navigation
     * @param CRestServer $server
     * @return array
     * @throws RestException
     */
    public function getList(array $parameters, $navigation, CRestServer $server): array
    {
        $filter = $parameters['filter'] ?? [];
        $order = $parameters['order'] ?? ['ID' => 'DESC'];

        $filter['CREATED_AT'] = CRestUtil::unConvertDateTime($filter['CREATED_AT']);
        $filter['COMPLETION_DATE'] = CRestUtil::unConvertDateTime($filter['COMPLETION_DATE']);
        $filter['ESTIMATED_COMPLETION_DATE'] = CRestUtil::unConvertDateTime($filter['ESTIMATED_COMPLETION_DATE']);

        $filter = new Filter(
            $filter['TITLE'] ?? null,
            DateRange::createFromArray($filter, 'CREATED_AT'),
            (array)($filter['RESPONSIBLE_ID'] ?? null),
            DateRange::createFromArray($filter, 'ESTIMATED_COMPLETION_DATE'),
            DateRange::createFromArray($filter, 'COMPLETION_DATE'),
        );

        $navigationParameters = static::getNavData($navigation, true);
        $pageNumber = (int)($navigationParameters['offset'] / $navigationParameters['limit'] + 1);

        try
        {
            $fragment = $this->projectService->getFragment(
                $filter,
                $order,
                $navigationParameters['limit'],
                $pageNumber,
            );

            return (array)static::setNavData(
                [
                    'projects' => $fragment->map($this->convertProject(...)),
                ], [
                    'offset' => $navigationParameters['offset'],
                    'count'  => $this->projectService->count($filter),
                ]
            );
        }
        catch (ServiceException $e)
        {
            throw new RestException($e->getMessage(), previous: $e);
        }
    }

    public function convertProject($project): array
    {
        return [
            'ID'                        => $project->id,
            'TITLE'                     => $project->title,
            'CREATED_AT'                => CRestUtil::ConvertDateTime($project->createdAt),
            'CREATED_BY'                => $project->createdBy,
            'UPDATED_AT'                => CRestUtil::ConvertDateTime($project->updatedAt),
            'UPDATED_BY'                => $project->updatedBy,
            'COMPLETION_DATE'           => CRestUtil::ConvertDateTime($project->completionDate),
            'ESTIMATED_COMPLETION_DATE' => CRestUtil::ConvertDateTime($project->estimatedCompletionDate),
            'DESCRIPTION'               => $project->description,
            'RESPONSIBLE_ID'            => $project->responsibleId,
            'COMMENT'                   => $project->comment,
            'INCOME'                    => $project->income,
        ];
    }
}