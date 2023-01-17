<?php
declare(strict_types=1);

namespace ClawRock\Faq\Api;

use ClawRock\Faq\Api\Data\QuestionInterface;
use ClawRock\Faq\Api\Data\QuestionSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface QuestionRepositoryInterface
{
    /**
     * @return \ClawRock\Faq\Api\Data\QuestionInterface
     */
    public function create(): QuestionInterface;

    /**
     * @param \ClawRock\Faq\Api\Data\QuestionInterface $question
     * @return void
     */
    public function save(QuestionInterface $question): void;

    /**
     * @param int $id
     * @return \ClawRock\Faq\Api\Data\QuestionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id): QuestionInterface;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \ClawRock\Faq\Api\Data\QuestionSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria): QuestionSearchResultsInterface;

    /**
     * @param \ClawRock\Faq\Api\Data\QuestionInterface $question
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(QuestionInterface $question): void;
}
