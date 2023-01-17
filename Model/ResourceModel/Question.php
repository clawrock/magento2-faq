<?php
declare(strict_types=1);

namespace ClawRock\Faq\Model\ResourceModel;

use ClawRock\Faq\Api\Data\QuestionInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Stdlib\DateTime;

class Question extends AbstractDb
{
    public const TABLE_NAME = 'clawrock_faq_question';
    public const CATEGORY_TABLE_NAME = 'clawrock_faq_question_category';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, QuestionInterface::QUESTION_ID);
    }

    protected function _afterLoad(AbstractModel $model)
    {
        if ($model->getId()) {
            $categories = $this->lookupCategoryIds((int) $model->getId());

            $model->setData(QuestionInterface::CATEGORY_ID, $categories);
        }

        return parent::_afterLoad($model);
    }

    protected function _beforeSave(AbstractModel $model)
    {
        $timestamp = (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT);
        if ($model->isObjectNew()) {
            $model->setData(QuestionInterface::CREATED_AT, $timestamp);
        }
        $model->setData(QuestionInterface::UPDATED_AT, $timestamp);

        return parent::_beforeSave($model);
    }

    public function lookupCategoryIds(int $questionId): array
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable(self::CATEGORY_TABLE_NAME), QuestionInterface::CATEGORY_ID)
            ->where('question_id = ?', (string) $questionId);

        return $this->getConnection()->fetchCol($select);
    }
}
