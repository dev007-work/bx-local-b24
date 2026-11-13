<?php

namespace B24\Project;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\ORM\Fields\FieldTypeMask;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;

class Service
{
    public const TRACKED_FIELDS = [
        'RESPONSIBLE_ID',
        'TITLE',
        'COMMENT',
        'COMPLETION_DATE',
        'ESTIMATED_COMPLETION_DATE',
        'DESCRIPTION',
        'INCOME',
    ];

    public function __construct(
        private readonly History\Service $historyService = new History\Service()
    )
    {}

    /**
     * @param Filter $filter
     * @return int
     * @throws ServiceException
     */
    public function count(Filter $filter): int
    {
        try
        {
            return ProjectTable::query()->where($filter->toCriteria())->queryCountTotal();
        }
        catch (SystemException $e)
        {
            throw new ServiceException('Failed to count projects', previous: $e);
        }
    }

    public function getFragment(Filter $filter, array $order, int $size, int $page): Collection
    {
        if ($size < 0) {
            throw new ServiceException('Page size MUST be a positive integer', 0);
        }

        $offset = (max($page, 1) - 1) * $size;

        return $this->findProjects($filter->toCriteria(), $order, $size, $offset);
    }

    /**
     * @param ConditionTree $criteria
     * @param array $order
     * @param int|null $limit
     * @param int|null $offset
     * @return Collection
     * @throws ServiceException
     */
    private function findProjects(
        ConditionTree $criteria,
        array $order,
        ?int $limit = null,
        ?int $offset = null
    ): Collection
    {
        try
        {
            $result = ProjectTable::query()
                ->setSelect([
                    'ID',
                    'TITLE',
                    'CREATED_AT',
                    'CREATED_BY',
                    'UPDATED_AT',
                    'UPDATED_BY',
                    'ESTIMATED_COMPLETION_DATE',
                    'COMPLETION_DATE',
                    'DESCRIPTION',
                    'RESPONSIBLE_ID',
                    'COMMENT',
                    'INCOME',
                ])
                ->where($criteria)
                ->setOrder($order)
                ->setLimit($limit)
                ->setOffset($offset)
                ->exec();

            $projects = new Collection();
            foreach ($result->fetchCollection() as $entityObject) {
                $projects->insert(new Project(
                    $entityObject->getId(),
                    $entityObject->getTitle(),
                    $entityObject->getCreatedAt(),
                    $entityObject->getCreatedBy(),
                    $entityObject->getUpdatedAt(),
                    $entityObject->getUpdatedBy(),
                    $entityObject->getEstimatedCompletionDate() ?: null,
                    $entityObject->getCompletionDate() ?: null,
                    $entityObject->getDescription(),
                    $entityObject->getResponsibleId(),
                    $entityObject->getComment(),
                    $entityObject->getIncome(),
                ));
            }

            return $projects;
        }
        catch (SystemException $e)
        {
            throw new ServiceException('Failed to find project', previous: $e);
        }
    }

    /**
     * @param Project $project
     * @return Project
     * @throws ServiceException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function create(Project $project): Project
    {
        $currentTime = new DateTime();
        $currentUser = CurrentUser::get()->getId();

        $project->createdBy = $currentUser;
        $project->createdAt = $currentTime;
        $project->updatedBy = $currentUser;
        $project->updatedAt = $currentTime;

        $entityObject = ProjectTable::createObject()
            ->setTitle($project->title)
            ->setCreatedAt($project->createdAt)
            ->setCreatedBy($project->createdBy)
            ->setUpdatedAt($project->updatedAt)
            ->setUpdatedBy($project->updatedBy)
            ->setEstimatedCompletionDate($project->estimatedCompletionDate ?? '')
            ->setCompletionDate($project->completionDate ?? '')
            ->setDescription($project->description)
            ->setResponsibleId($project->responsibleId)
            ->setComment($project->comment)
            ->setIncome($project->income);

        $addResult = $entityObject->save();

        if (!$addResult->isSuccess()) {
            throw new ServiceException('Failed to add project', 0);
        }

        $project = $project->withId($addResult->getId());
        $history = array_filter(
            $entityObject->collectValues(fieldsMask: FieldTypeMask::FLAT),
            static fn(string $fieldName): bool => in_array($fieldName, Service::TRACKED_FIELDS, true),
            ARRAY_FILTER_USE_KEY,
        );

        foreach ($history as $field => $value) {
            $this->historyService->register(new History\Entry(
                null,
                $project->id,
                $project->responsibleId,
                $field,
                null,
                $value,
                $project->createdAt,
            ));
        }

        return $project;
    }

    public function update(Project $project): void
    {
        $currentTime = new DateTime();
        $currentUser = CurrentUser::get()->getId();

        $project->updatedBy = $currentUser;
        $project->updatedAt = $currentTime;

        $actualProject = ProjectTable::getById($project->id)->fetchObject();
        $entityObject = $actualProject
            ->setTitle($project->title)
            ->setCreatedAt($project->createdAt)
            ->setCreatedBy($project->createdBy)
            ->setUpdatedAt($project->updatedAt)
            ->setUpdatedBy($project->updatedBy)
            ->setEstimatedCompletionDate($project->estimatedCompletionDate ?? '')
            ->setCompletionDate($project->completionDate ?? '')
            ->setDescription($project->description)
            ->setResponsibleId($project->responsibleId)
            ->setComment($project->comment)
            ->setIncome($project->income);

        $history = [];
        foreach ($entityObject->collectValues(fieldsMask: FieldTypeMask::FLAT) as $field => $currentValue) {
            if (!in_array($field, Service::TRACKED_FIELDS, true)) {
                continue;
            }

            if (!$entityObject->isChanged($field)) {
                continue;
            }

            $history[] = new History\Entry(
                null,
                $project->id,
                $project->updatedBy,
                $field,
                $entityObject->remindActual($field),
                $currentValue,
                $project->updatedAt,
            );
        }

        $addResult = $entityObject->save();

        if (!$addResult->isSuccess()) {
            throw new ServiceException('Failed to add project');
        }

        foreach ($history as $item) {
            $this->historyService->register($item);
        }
    }

    public function delete(Project $project): void
    {
        $deleteResult = ProjectTable::getById($project->id)->fetchObject()->delete();

        if (!$deleteResult->isSuccess()) {
            throw new ServiceException('Failed to delete project', 0);
        }

        $this->historyService->clearByProject($project);
    }

    public function getById(int $projectId): ?Project
    {
        $criteria = new ConditionTree();
        $criteria->where('ID', '=', $projectId);

        $projects = $this->findProjects($criteria, ['ID' => 'DESC']);

        if ($projects->isEmpty()) {
            return null;
        }

        return $projects->get($projectId);
    }

    public function getByIds(int ...$projectIds): Collection
    {
        if (empty($projectIds)) {
            return new Collection();
        }

        $criteria = new ConditionTree();
        $criteria->whereIn('ID', $projectIds);

        return $this->findProjects($criteria, ['ID' => 'DESC']);
    }
}