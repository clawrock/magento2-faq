<?php
declare(strict_types=1);

namespace ClawRock\Faq\Ui\DataProvider;

use ClawRock\Faq\Api\Data\CategoryInterface;
use Magento\Framework\Api\Search\SearchResultInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class CategoryDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    private \ClawRock\Faq\Api\CategoryRepositoryInterface $categoryRepository;
    private \Magento\Ui\DataProvider\SearchResultFactory $searchResultFactory;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Api\Search\ReportingInterface $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \ClawRock\Faq\Api\CategoryRepositoryInterface $categoryRepository,
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

        $this->categoryRepository = $categoryRepository;
        $this->searchResultFactory = $searchResultFactory;
    }

    public function getData(): array
    {
        $data = parent::getData();
        if ('category_form_data_source' === $this->name) {
            if ($data['totalRecords'] > 0) {
                $categoryId = (int) $data['items'][0][CategoryInterface::CATEGORY_ID];
                $data = [$categoryId => $data['items'][0]];
            } else {
                $data = [];
            }
        }

        return $data;
    }

    public function getSearchResult(): SearchResultInterface
    {
        $searchCriteria = $this->getSearchCriteria();
        $result = $this->categoryRepository->getList($searchCriteria);

        return $this->searchResultFactory->create(
            $result->getItems(),
            $result->getTotalCount(),
            $searchCriteria,
            CategoryInterface::CATEGORY_ID
        );
    }
}
