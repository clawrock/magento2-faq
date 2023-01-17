<?php
declare(strict_types=1);

namespace ClawRock\Faq\Controller\Adminhtml\Question;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;

class MassDelete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'ClawRock_Faq::question_delete';

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

    public function execute(): ?ResultInterface
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $deletedCount = 0;
        foreach ($collection as $item) {
            try {
                $item->delete();
                $deletedCount++;
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('An error occurred during category deletion'));
            }
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $deletedCount));

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
