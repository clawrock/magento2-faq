<?php
declare(strict_types=1);

namespace ClawRock\Faq\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class CategoryOptionSource implements OptionSourceInterface
{
    private ?array $options = null;
    private \Magento\Framework\Escaper $escaper;
    private \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder;
    private \ClawRock\Faq\Api\CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \ClawRock\Faq\Api\CategoryRepositoryInterface $categoryRepository
    ) {
        $this->escaper = $escaper;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->categoryRepository = $categoryRepository;
    }

    public function toOptionArray(): array
    {
        if ($this->options === null) {
            $this->options = [];

            $searchCriteria = $this->searchCriteriaBuilder->create();
            $categories = $this->categoryRepository->getList($searchCriteria)->getItems();
            foreach ($categories as $category) {
                $this->options[] = [
                    'label' => $this->escaper->escapeHtml($category->getName()),
                    'value' => $category->getCategoryId(),
                ];
            }
        }

        return $this->options;
    }
}
