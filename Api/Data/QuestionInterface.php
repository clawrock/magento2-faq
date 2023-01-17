<?php
declare(strict_types=1);

namespace ClawRock\Faq\Api\Data;

interface QuestionInterface
{
    public const QUESTION_ID = 'question_id';
    public const QUESTION = 'question';
    public const ANSWER = 'answer';
    public const SORT_ORDER = 'sort_order';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const ACTIVE = 'active';
    public const CATEGORY_ID = 'category_id';

    public const ENABLED  = 1;
    public const DISABLED = 0;

    /**
     * @return int
     */
    public function getQuestionId(): int;

    /**
     * @param int $id
     * @return void
     */
    public function setQuestionId(int $id): void;

    /**
     * @return string
     */
    public function getQuestion(): string;

    /**
     * @param string $question
     * @return void
     */
    public function setQuestion(string $question): void;

    /**
     * @return string
     */
    public function getAnswer(): string;

    /**
     * @param string $answer
     * @return void
     */
    public function setAnswer(string $answer): void;

    /**
     * @return int
     */
    public function getSortOrder(): int;

    /**
     * @param int $sortOrder
     * @return void
     */
    public function setSortOrder(int $sortOrder): void;

    /**
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * @param string $createdAt
     * @return void
     */
    public function setCreatedAt(string $createdAt): void;

    /**
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * @param string $updatedAt
     * @return void
     */
    public function setUpdatedAt(string $updatedAt): void;

    /**
     * @return bool
     */
    public function isActive(): bool;

    /**
     * @param bool $isActive
     * @return void
     */
    public function setActive(bool $isActive): void;

    /**
     * @return string[]
     */
    public function getCategoryId(): array;

    /**
     * @param string[] $categoryIds
     * @return void
     */
    public function setCategoryId(array $categoryIds): void;
}
