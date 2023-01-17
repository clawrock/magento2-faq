<?php
declare(strict_types=1);

namespace ClawRock\Faq\Api;

use ClawRock\Faq\Api\Data\CategoryInterface;
use ClawRock\Faq\Api\Data\CategorySearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface CategoryRepositoryInterface
{
    /**
     * @return \ClawRock\Faq\Api\Data\CategoryInterface
     */
    public function create(): CategoryInterface;

    /**
     * @param \ClawRock\Faq\Api\Data\CategoryInterface $category
     * @return void
     */
    public function save(CategoryInterface $category): void;

    /**
     * @param int $id
     * @return \ClawRock\Faq\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id): CategoryInterface;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \ClawRock\Faq\Api\Data\CategorySearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria): CategorySearchResultsInterface;

    /**
     * @param \ClawRock\Faq\Api\Data\CategoryInterface $category
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(CategoryInterface $category): void;
}
