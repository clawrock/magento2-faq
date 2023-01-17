<?php
declare(strict_types=1);

namespace ClawRock\Faq\Model;

use ClawRock\Faq\Api\Data\QuestionInterface;
use ClawRock\Faq\Api\Data\QuestionSearchResultsInterface;
use ClawRock\Faq\Model\ResourceModel\Question;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class QuestionRepository implements \ClawRock\Faq\Api\QuestionRepositoryInterface
{
    private \ClawRock\Faq\Model\ResourceModel\Question $resource;
    private \ClawRock\Faq\Api\Data\QuestionInterfaceFactory $questionFactory;
    private \ClawRock\Faq\Model\ResourceModel\Question\CollectionFactory $collectionFactory;
    private \ClawRock\Faq\Api\Data\QuestionSearchResultsInterfaceFactory $searchResultsFactory;
    private \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor;

    public function __construct(
        \ClawRock\Faq\Model\ResourceModel\Question $resource,
        \ClawRock\Faq\Api\Data\QuestionInterfaceFactory $questionFactory,
        \ClawRock\Faq\Model\ResourceModel\Question\CollectionFactory $collectionFactory,
        \ClawRock\Faq\Api\Data\QuestionSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->questionFactory = $questionFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function create(): QuestionInterface
    {
        return $this->questionFactory->create();
    }

    public function save(QuestionInterface $question): void
    {
        try {
            $this->resource->save($question); // @phpstan-ignore-line
            $this->saveCategories($question);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the question: %1', $exception->getMessage()),
                $exception
            );
        }
    }

    public function getById(int $id): QuestionInterface
    {
        $question = $this->questionFactory->create();

        $question->load($id); // @phpstan-ignore-line
        if (!$question->getQuestionId()) {
            throw new NoSuchEntityException(__('FAQ question with id "%1" does not exist.', $id));
        }

        return $question;
    }

    public function getList(SearchCriteriaInterface $criteria): QuestionSearchResultsInterface
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

    public function delete(QuestionInterface $question): void
    {
        try {
            $this->resource->delete($question); // @phpstan-ignore-line
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the question: %1',
                $exception->getMessage()
            ));
        }
    }

    private function saveCategories(QuestionInterface $question): void
    {
        $currentCategories = $this->resource->lookupCategoryIds($question->getQuestionId());
        $newCategories = $question->getCategoryId();
        $insert = array_diff($newCategories, $currentCategories);
        $delete = array_diff($currentCategories, $newCategories);

        if ($delete) {
            $where = [
                'question_id = ?' => $question->getQuestionId(),
                'category_id IN (?)' => $delete,
            ];

            $this->resource->getConnection()->delete($this->resource->getTable(Question::CATEGORY_TABLE_NAME), $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $categoryId) {
                $data[] = [
                    'question_id' => $question->getQuestionId(),
                    'category_id'    => (int) $categoryId,
                ];
            }

            $this->resource->getConnection()->insertMultiple(
                $this->resource->getTable(Question::CATEGORY_TABLE_NAME),
                $data
            );
        }
    }
}
