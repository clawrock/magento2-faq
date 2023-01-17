<?php
declare(strict_types=1);

namespace ClawRock\Faq\Ui\DataProvider;

use ClawRock\Faq\Api\Data\QuestionInterface;
use Magento\Framework\Api\Search\SearchResultInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class QuestionDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    private \ClawRock\Faq\Api\QuestionRepositoryInterface $questionRepository;
    private \Magento\Ui\DataProvider\SearchResultFactory $searchResultFactory;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Api\Search\ReportingInterface $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \ClawRock\Faq\Api\QuestionRepositoryInterface $questionRepository,
        \Magento\Ui\DataProvider\SearchResultFactory $searchResultFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );

        $this->questionRepository = $questionRepository;
        $this->searchResultFactory = $searchResultFactory;
    }

    public function getData(): array
    {
        $data = parent::getData();
        if ('question_form_data_source' === $this->name) {
            if ($data['totalRecords'] > 0) {
                $questionId = (int) $data['items'][0][QuestionInterface::QUESTION_ID];
                $data = [$questionId => $data['items'][0]];
            } else {
                $data = [];
            }
        }

        return $data;
    }

    public function getSearchResult(): SearchResultInterface
    {
        $searchCriteria = $this->getSearchCriteria();
        $result = $this->questionRepository->getList($searchCriteria);

        return $this->searchResultFactory->create(
            $result->getItems(),
            $result->getTotalCount(),
            $searchCriteria,
            QuestionInterface::QUESTION_ID
        );
    }
}
