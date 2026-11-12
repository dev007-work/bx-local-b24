<?php

namespace B24\Project;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;

class Service
{
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

        return $project->withId($addResult->getId());
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

        $addResult = $entityObject->save();

        if (!$addResult->isSuccess()) {
            throw new ServiceException('Failed to add project');
        }
    }

    public function delete(Project $project): void
    {
        $deleteResult = ProjectTable::getById($project->id)->fetchObject()->delete();

        if (!$deleteResult->isSuccess()) {
            throw new ServiceException('Failed to delete project', 0);
        }
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

    }
}