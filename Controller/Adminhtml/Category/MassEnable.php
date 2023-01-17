<?php
declare(strict_types=1);

namespace ClawRock\Faq\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;

class MassEnable extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'ClawRock_Faq::category_save';

    private \Magento\Ui\Component\MassAction\Filter $filter;
    private \ClawRock\Faq\Model\ResourceModel\Category\CollectionFactory $collectionFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \ClawRock\Faq\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
        \Magento\Ui\Component\MassAction\Filter $filter
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    public function execute(): ?ResultInterface
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $enabledCount = 0;
        foreach ($collection as $item) {
            try {
                $item->setActive(true);
                $item->save();
                $enabledCount++;
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('An error occurred during category enabling, ID: %1', $item->getId())
                );
            }
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been enabled.', $enabledCount));

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
