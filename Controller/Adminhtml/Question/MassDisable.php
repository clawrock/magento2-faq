<?php
declare(strict_types=1);

namespace ClawRock\Faq\Controller\Adminhtml\Question;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;

class MassDisable extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'ClawRock_Faq::question_save';

    private \Magento\Ui\Component\MassAction\Filter $filter;
    private \ClawRock\Faq\Model\ResourceModel\Question\CollectionFactory $collectionFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \ClawRock\Faq\Model\ResourceModel\Question\CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $disabledCount = 0;
        foreach ($collection as $item) {
            try {
                $item->setActive(false);
                $item->save();
                $disabledCount++;
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('An error occurred during question disabling, ID: %1', $item->getId())
                );
            }
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been disabled.', $disabledCount));

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
