<?php
declare(strict_types=1);

namespace ClawRock\Faq\Model;

use ClawRock\Faq\Api\Data\QuestionInterface;
use Magento\Framework\Model\AbstractModel;

class Question extends AbstractModel implements QuestionInterface
{
    protected function _construct()
    {
        $this->_init(\ClawRock\Faq\Model\ResourceModel\Question::class);
    }

    public function getQuestionId(): int
    {
        return (int) $this->getData(self::QUESTION_ID);
    }

    public function setQuestionId(int $id): void
    {
        $this->setData(self::QUESTION_ID, $id);
    }

    public function getQuestion(): string
    {
        return (string) $this->getData(self::QUESTION);
    }

    public function setQuestion(string $question): void
    {
        $this->setData(self::QUESTION, $question);
    }

    public function getAnswer(): string
    {
        return (string) $this->getData(self::ANSWER);
    }

    public function setAnswer(string $answer): void
    {
        $this->setData(self::ANSWER, $answer);
    }

    public function getSortOrder(): int
    {
        return (int) $this->getData(self::SORT_ORDER);
    }

    public function setSortOrder(int $sortOrder): void
    {
        $this->setData(self::SORT_ORDER, $sortOrder);
    }

    public function getCreatedAt(): string
    {
        return (string) $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getUpdatedAt(): string
    {
        return (string) $this->getData(self::UPDATED_AT);
    }

    public function setUpdatedAt(string $updatedAt): void
    {
        $this->setData(self::UPDATED_AT, $updatedAt);
    }

    public function isActive(): bool
    {
        return (bool) $this->getData(self::ACTIVE);
    }

    public function setActive(bool $isActive): void
    {
        $this->setData(self::ACTIVE, $isActive);
    }

    public function getCategoryId(): array
    {
        return $this->getData(self::CATEGORY_ID) ?? [];
    }

    public function setCategoryId(array $categoryIds): void
    {
        $this->setData(self::CATEGORY_ID, $categoryIds);
    }
}
