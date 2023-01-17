<?php
declare(strict_types=1);

namespace ClawRock\Faq\Api\Data;

interface CategoryInterface
{
    public const CATEGORY_ID = 'category_id';
    public const NAME = 'name';
    public const SORT_ORDER = 'sort_order';
    public const ACTIVE = 'active';
    public const STORE_ID = 'store_id';

    public const ENABLED = 1;
    public const DISABLED = 0;

    /**
     * @return int
     */
    public function getCategoryId(): int;

    /**
     * @param int $id
     * @return void
     */
    public function setCategoryId(int $id): void;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void;

    /**
     * @return int
     */
    public function getSortOrder(): int;

    /**
     * @param int $sortOrder
     * @return void
     */
    public function setSortOrder(int $sortOrder): void;

    /**
     * @return bool
     */
    public function isActive(): bool;

    /**
     * @param bool $isActive
     * @return void
     */
    public function setActive(bool $isActive): void;

    /**
     * @return string[]
     */
    public function getStoreId(): array;

    /**
     * @param string[] $storeIds
     * @return void
     */
    public function setStoreId(array $storeIds): void;
}
