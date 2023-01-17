<?php
declare(strict_types=1);

namespace ClawRock\Faq\Controller\Adminhtml\Category;

use ClawRock\Faq\Api\Data\CategoryInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'ClawRock_Faq::category_save';

    private \Magento\Framework\Registry $coreRegistry;
    private \ClawRock\Faq\Api\CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $registry,
        \ClawRock\Faq\Api\CategoryRepositoryInterface $categoryRepository
    ) {
        $this->coreRegistry = $registry;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context);
    }

    public function execute(): ?ResultInterface
    {
        $id = (int) $this->getRequest()->getParam(CategoryInterface::CATEGORY_ID);

        try {
            $category = $id ? $this->categoryRepository->getById($id) : $this->categoryRepository->create();
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This category no longer exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        $this->coreRegistry->register('clawrock_faq_category', $category);
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($resultPage instanceof \Magento\Backend\Model\View\Result\Page) {
            $resultPage->setActiveMenu('ClawRock_Faq::category')
                ->addBreadcrumb((string) __('FAQ'), (string) __('FAQ'))
                ->addBreadcrumb((string) __('Manage Categories'), (string) __('Manage Categories'))
                ->addBreadcrumb(
                    (string) ($id ? __('Edit Category') : __('New Category')),
                    (string) ($id ? __('Edit Category') : __('New Category'))
                );
        }
        $resultPage->getConfig()->getTitle()->prepend((string) __('Categories'));
        $resultPage->getConfig()->getTitle()
            ->prepend($category->getCategoryId() ? $category->getName() : (string) __('New Category'));

        return $resultPage;
    }
}
