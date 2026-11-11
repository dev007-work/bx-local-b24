<?php

namespace B24\Project;

use Bitrix\Main\Type\DateTime;

class Project
{
    public function __construct(
        public readonly ?int $id,
        public string $title,
        public ?DateTime $createdAt,
        public ?int $createdBy,
        public ?DateTime $updatedAt,
        public ?int $updatedBy,
        public ?DateTime $estimatedCompletionDate,
        public ?DateTime $completionDate,
        public string $description,
        public int $responsibleId,
        public string $comment,
        public string $income,
    )
    {}

    /**
     * Метод для клонирования сущности проекта с заданным ID
     *
     * @param int $id
     * @return self
     */
    public function withId(int $id): self
    {
        return new Project(
            $id,
            $this->title,
            $this->createdAt,
            $this->createdBy,
            $this->updatedAt,
            $this->updatedBy,
            $this->estimatedCompletionDate,
            $this->completionDate,
            $this->description,
            $this->responsibleId,
            $this->comment,
            $this->income,
        );
    }
}