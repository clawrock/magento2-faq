<?php
declare(strict_types=1);

namespace ClawRock\Faq\Model\ResourceModel;

use ClawRock\Faq\Api\Data\CategoryInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Category extends AbstractDb
{
    public const TABLE_NAME       = 'clawrock_faq_category';
    public const STORE_TABLE_NAME = 'clawrock_faq_category_store';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, CategoryInterface::CATEGORY_ID);
    }

    protected function _afterLoad(AbstractModel $model): Category
    {
        if ($model->getId()) {
            $stores = $this->lookupStoreIds((int) $model->getId());

            $model->setData(CategoryInterface::STORE_ID, $stores);
        }

        return parent::_afterLoad($model);
    }

    public function lookupStoreIds(int $categoryId): array
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable(self::STORE_TABLE_NAME), CategoryInterface::STORE_ID)
            ->where('category_id = ?', (string) $categoryId);

        return $this->getConnection()->fetchCol($select);
    }
}
