<?php
declare(strict_types=1);

namespace ClawRock\Faq\Model;

use ClawRock\Faq\Api\CategoryRepositoryInterface;
use ClawRock\Faq\Api\Data\CategoryInterface;
use ClawRock\Faq\Api\Data\CategorySearchResultsInterface;
use ClawRock\Faq\Model\ResourceModel\Category;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CategoryRepository implements CategoryRepositoryInterface
{
    private \ClawRock\Faq\Model\ResourceModel\Category $resource;
    private \ClawRock\Faq\Api\Data\CategoryInterfaceFactory $categoryFactory;
    private \ClawRock\Faq\Model\ResourceModel\Category\CollectionFactory $collectionFactory;
    private \ClawRock\Faq\Api\Data\CategorySearchResultsInterfaceFactory $searchResultsFactory;
    private \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor;

    public function __construct(
        \ClawRock\Faq\Model\ResourceModel\Category $resource,
        \ClawRock\Faq\Api\Data\CategoryInterfaceFactory $categoryFactory,
        \ClawRock\Faq\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
        \ClawRock\Faq\Api\Data\CategorySearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->categoryFactory = $categoryFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function create(): CategoryInterface
    {
        return $this->categoryFactory->create();
    }

    public function save(CategoryInterface $category): void
    {
        try {
            $this->resource->save($category); // @phpstan-ignore-line
            $this->saveStores($category);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the category: %1', $exception->getMessage()),
                $exception
            );
        }
    }

    public function getById(int $id): CategoryInterface
    {
        $category = $this->categoryFactory->create();

        $category->load($id); // @phpstan-ignore-line
        if (!$category->getCategoryId()) {
            throw new NoSuchEntityException(__('FAQ category with id "%1" does not exist.', $id));
        }

        return $category;
    }

    public function getList(SearchCriteriaInterface $criteria): CategorySearchResultsInterface
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($criteria, $collection);
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $items = $collection->getItems();
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @param \ClawRock\Faq\Api\Data\CategoryInterface $category
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(CategoryInterface $category): void
    {
        try {
            $this->resource->delete($category);  // @phpstan-ignore-line
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the category: %1',
                $exception->getMessage()
            ));
        }
    }

    private function saveStores(CategoryInterface $category): void
    {
        $currentStores = $this->resource->lookupStoreIds($category->getCategoryId());
        $newStores = $category->getStoreId();
        $insert = array_diff($newStores, $currentStores);
        $delete = array_diff($currentStores, $newStores);

        if ($delete) {
            $where = [
                'category_id = ?' => $category->getCategoryId(),
                'store_id IN (?)' => $delete,
            ];

            $this->resource->getConnection()->delete($this->resource->getTable(Category::STORE_TABLE_NAME), $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $storeId) {
                $data[] = [
                    'category_id' => $category->getCategoryId(),
                    'store_id'    => (int) $storeId,
                ];
            }

            $this->resource->getConnection()->insertMultiple(
                $this->resource->getTable(Category::STORE_TABLE_NAME),
                $data
            );
        }
    }
}
