<?php
declare(strict_types=1);

namespace ClawRock\Faq\Api\Data;

interface CategorySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \ClawRock\Faq\Api\Data\CategoryInterface[]
     */
    public function getItems();

    /**
     * @param \ClawRock\Faq\Api\Data\CategoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
