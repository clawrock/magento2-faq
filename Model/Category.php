<?php
declare(strict_types=1);

namespace ClawRock\Faq\Model;

use ClawRock\Faq\Api\Data\CategoryInterface;
use Magento\Framework\Model\AbstractModel;

class Category extends AbstractModel implements CategoryInterface
{
    protected function _construct()
    {
        $this->_init(\ClawRock\Faq\Model\ResourceModel\Category::class);
    }

    public function getCategoryId(): int
    {
        return (int) $this->getData(self::CATEGORY_ID);
    }

    public function setCategoryId(int $id): void
    {
        $this->setData(self::CATEGORY_ID, $id);
    }

    public function getName(): string
    {
        return (string) $this->getData(self::NAME);
    }

    public function setName(string $name): void
    {
        $this->setData(self::NAME, $name);
    }

    public function getSortOrder(): int
    {
        return (int) $this->getData(self::SORT_ORDER);
    }

    public function setSortOrder(int $sortOrder): void
    {
        $this->setData(self::SORT_ORDER, $sortOrder);
    }

    public function isActive(): bool
    {
        return (bool) $this->getData(self::ACTIVE);
    }

    public function setActive(bool $isActive): void
    {
        $this->setData(self::ACTIVE, $isActive);
    }

    public function getStoreId(): array
    {
        return $this->getData(self::STORE_ID) ?? [];
    }

    public function setStoreId(array $storeIds): void
    {
        $this->setData(self::STORE_ID, $storeIds);
    }
}
