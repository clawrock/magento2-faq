<?php
declare(strict_types=1);

namespace ClawRock\Faq\Model\Api\SearchCriteria\FilterProcessor;

use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;

class CurrentStoreFilter implements CustomFilterInterface
{
    private \Magento\Store\Model\StoreManagerInterface $storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magento\Framework\Api\Filter $filter
     * @param \ClawRock\Faq\Model\ResourceModel\Category\Collection $collection
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function apply(
        \Magento\Framework\Api\Filter $filter,
        \Magento\Framework\Data\Collection\AbstractDb $collection
    ) {
        $collection->addStoreFilter((int) $this->storeManager->getStore()->getId());

        return true;
    }
}
