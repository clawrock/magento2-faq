<?php
declare(strict_types=1);

namespace ClawRock\Faq\Model\Api\SearchCriteria\FilterProcessor;

use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;

class QuestionCategoryFilter implements CustomFilterInterface
{
    public function apply(
        \Magento\Framework\Api\Filter $filter,
        \Magento\Framework\Data\Collection\AbstractDb $collection
    ) {
        if ($filter->getValue() && $collection instanceof \ClawRock\Faq\Model\ResourceModel\Question\Collection) {
            $collection->addCategoryFilter($filter->getValue());
        }

        return true;
    }
}
