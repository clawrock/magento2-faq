<?php
declare(strict_types=1);

namespace ClawRock\Faq\Model\Api\SearchCriteria\JoinProcessor;

use Magento\Framework\Api\SearchCriteria\CollectionProcessor\JoinProcessor\CustomJoinInterface;
use Magento\Framework\Data\Collection\AbstractDb;

class QuestionCurrentStore implements CustomJoinInterface
{
    private \Magento\Store\Model\StoreManagerInterface $storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    public function apply(AbstractDb $collection)
    {
        if ($collection instanceof \ClawRock\Faq\Model\ResourceModel\Question\Collection) {
            $collection->addStoreFilter((int) $this->storeManager->getStore()->getId());
        }

        return true;
    }
}
