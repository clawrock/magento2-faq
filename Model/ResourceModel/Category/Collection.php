<?php
declare(strict_types=1);

namespace ClawRock\Faq\Model\ResourceModel\Category;

use ClawRock\Faq\Api\Data\CategoryInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\Store;

/**
 * @method \ClawRock\Faq\Api\Data\CategoryInterface[] getItems()
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = CategoryInterface::CATEGORY_ID;
    private \Magento\Store\Model\StoreManagerInterface $storeManager;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ?\Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        ?\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);

        $this->storeManager = $storeManager;
    }

    protected function _construct()
    {
        $this->_init(\ClawRock\Faq\Model\Category::class, \ClawRock\Faq\Model\ResourceModel\Category::class);
        $this->_map['fields'][CategoryInterface::CATEGORY_ID] = 'main_table.category_id';
        $this->_map['fields'][CategoryInterface::STORE_ID] = 'store_table.store_id';
    }

    /**
     * @param array|int|string|\Magento\Store\Api\Data\StoreInterface $store
     * @param bool $withAdmin
     * @return \ClawRock\Faq\Model\ResourceModel\Category\Collection
     */
    public function addStoreFilter($store, bool $withAdmin = true): Collection
    {
        if (!$this->getFlag('store_id_filter_added')) {
            if ($store instanceof Store) {
                $store = [$store->getId()];
            }

            if (!is_array($store)) {
                $store = [$store];
            }

            if ($withAdmin) {
                $store[] = Store::DEFAULT_STORE_ID;
            }

            $this->addFilter(CategoryInterface::STORE_ID, ['in' => $store], 'public'); // @phpstan-ignore-line
        }

        return $this;
    }

    /**
     * @param array|string $field
     * @param null|string|array $condition
     * @return \ClawRock\Faq\Model\ResourceModel\Category\Collection
     */
    public function addFieldToFilter($field, $condition = null): Collection
    {
        if ($field === CategoryInterface::STORE_ID && $condition !== null) {
            return $this->addStoreFilter($condition, false);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    protected function _afterLoad(): Collection
    {
        $linkField = 'category_id';
        $linkedIds = $this->getColumnValues($linkField);
        if (!empty($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(
                ['faq_entity_store' => $this->getTable('clawrock_faq_category_store')]
            )->where('faq_entity_store.' . $linkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);
            if ($result) {
                $storesData = [];
                foreach ($result as $storeData) {
                    $storesData[$storeData[$linkField]][] = $storeData['store_id'];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($storesData[$linkedId])) {
                        continue;
                    }
                    $storeIdKey = array_search(Store::DEFAULT_STORE_ID, $storesData[$linkedId], true);
                    if ($storeIdKey !== false) {
                        $stores = $this->storeManager->getStores(false, true);
                        $storeId = current($stores)->getId(); // @phpstan-ignore-line
                        $storeCode = key($stores);
                    } else {
                        $storeId = current($storesData[$linkedId]);
                        $storeCode = $this->storeManager->getStore($storeId)->getCode();
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $storeCode);
                    $item->setData('store_id', $storesData[$linkedId]);
                }
            }
        }

        return parent::_afterLoad();
    }

    protected function _renderFiltersBefore(): void
    {
        $linkField = 'category_id';

        if ($this->getFilter(CategoryInterface::STORE_ID)) {
            $this->getSelect()->join(
                ['store_table' => $this->getTable('clawrock_faq_category_store')],
                'main_table.' . $linkField . ' = store_table.' . $linkField,
                []
            )->group(
                'main_table.' . $linkField
            );
        }

        parent::_renderFiltersBefore();
    }

    public function getSelectCountSql(): Select
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Select::GROUP);

        return $countSelect;
    }
}
