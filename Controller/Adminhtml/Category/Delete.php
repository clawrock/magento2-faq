<?php
declare(strict_types=1);

namespace ClawRock\Faq\Controller\Adminhtml\Category;

use ClawRock\Faq\Api\Data\CategoryInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;

class Delete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'ClawRock_Faq::category_delete';

    private \ClawRock\Faq\Api\CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \ClawRock\Faq\Api\CategoryRepositoryInterface $categoryRepository
    ) {
        parent::__construct($context);
        $this->categoryRepository = $categoryRepository;
    }

    public function execute(): ?ResultInterface
    {
        $id = (int) $this->getRequest()->getParam(CategoryInterface::CATEGORY_ID);
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $this->categoryRepository->delete($this->categoryRepository->getById($id));
            $this->messageManager->addSuccessMessage(__('The category has been deleted.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('*/*/');
    }
}
