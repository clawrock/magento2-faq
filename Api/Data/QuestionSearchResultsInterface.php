<?php
declare(strict_types=1);

namespace ClawRock\Faq\Api\Data;

interface QuestionSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \ClawRock\Faq\Api\Data\QuestionInterface[]
     */
    public function getItems();

    /**
     * @param \ClawRock\Faq\Api\Data\QuestionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
