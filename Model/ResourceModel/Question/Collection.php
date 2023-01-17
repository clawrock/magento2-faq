<?php
declare(strict_types=1);

namespace ClawRock\Faq\Model\ResourceModel\Question;

use ClawRock\Faq\Api\Data\CategoryInterface;
use ClawRock\Faq\Api\Data\QuestionInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @method \ClawRock\Faq\Api\Data\QuestionInterface[] getItems()
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = QuestionInterface::QUESTION_ID;

    protected function _construct()
    {
        $this->_init(\ClawRock\Faq\Model\Question::class, \ClawRock\Faq\Model\ResourceModel\Question::class);
        $this->_map['fields'][QuestionInterface::QUESTION_ID] = 'main_table.question_id';
        $this->_map['fields'][QuestionInterface::CATEGORY_ID] = 'category_table.category_id';
    }

    /**
     * @param \ClawRock\Faq\Api\Data\CategoryInterface|array|string $category
     * @return void
     */
    public function addCategoryFilter($category): void
    {
        if (!$this->getFlag('category_id_filter_added')) {
            if ($category instanceof CategoryInterface) {
                $category = [$category->getCategoryId()];
            }

            if (!is_array($category)) {
                $category = [$category];
            }

            $this->addFilter(QuestionInterface::CATEGORY_ID, ['in' => $category], 'public'); // @phpstan-ignore-line
        }
    }

    public function addStoreFilter(int $storeId): void
    {
        $this->getSelect()->join(
            ['question_category_table' => $this->getTable(
                \ClawRock\Faq\Model\ResourceModel\Question::CATEGORY_TABLE_NAME
            )],
            'main_table.'
            . QuestionInterface::QUESTION_ID
            . ' = question_category_table.'
            . QuestionInterface::QUESTION_ID,
            []
        )->join(
            ['category_store_table' => $this->getTable(
                \ClawRock\Faq\Model\ResourceModel\Category::STORE_TABLE_NAME
            )],
            'category_store_table.'
            . CategoryInterface::CATEGORY_ID
            . ' = question_category_table.' . CategoryInterface::CATEGORY_ID . ' AND '
            . '(category_store_table.' . CategoryInterface::STORE_ID
            . ' = ' . $storeId . ' OR category_store_table.'
            . CategoryInterface::STORE_ID . ' = 0)',
            []
        )->group(
            'main_table.' . QuestionInterface::QUESTION_ID
        );
    }

    /**
     * @param array|string $field
     * @param null|string $condition
     * @return \ClawRock\Faq\Model\ResourceModel\Question\Collection
     */
    public function addFieldToFilter($field, $condition = null): Collection
    {
        if ($field === QuestionInterface::CATEGORY_ID && $condition !== null) {
            $this->addCategoryFilter($condition);

            return $this;
        }

        return parent::addFieldToFilter($field, $condition);
    }

    protected function _afterLoad(): Collection
    {
        $linkField = 'question_id';
        $linkedIds = $this->getColumnValues($linkField);
        if (!empty($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(
                ['faq_entity_category' => $this->getTable('clawrock_faq_question_category')]
            )->where('faq_entity_category.' . $linkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);
            if ($result) {
                $categoriesData = [];
                foreach ($result as $categoryData) {
                    $categoriesData[$categoryData[$linkField]][] = $categoryData['category_id'];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($categoriesData[$linkedId])) {
                        continue;
                    }
                    $item->setData('category_id', $categoriesData[$linkedId]);
                }
            }
        }

        return parent::_afterLoad();
    }

    protected function _renderFiltersBefore(): void
    {
        if ($this->getFilter(QuestionInterface::CATEGORY_ID)) {
            $linkField = 'question_id';
            $this->getSelect()->join(
                ['category_table' => $this->getTable('clawrock_faq_question_category')],
                'main_table.' . $linkField . ' = category_table.' . $linkField,
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
