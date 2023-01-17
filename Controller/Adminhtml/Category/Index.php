<?php
declare(strict_types=1);

namespace ClawRock\Faq\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Index extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'ClawRock_Faq::category';

    private \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
    }

    public function execute(): ?ResultInterface
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($resultPage instanceof \Magento\Backend\Model\View\Result\Page) {
            $resultPage->setActiveMenu('ClawRock_Faq::category');
            $resultPage->addBreadcrumb((string) __('FAQ'), (string) __('FAQ'));
            $resultPage->addBreadcrumb((string) __('Manage Categories'), (string) __('Manage Categories'));
            $resultPage->getConfig()->getTitle()->prepend((string) __('Categories'));
        }

        $this->dataPersistor->clear('clawrock_faq_category');

        return $resultPage;
    }
}
