<?php
declare(strict_types=1);

namespace ClawRock\Faq\Controller\Adminhtml\Category;

use ClawRock\Faq\Api\Data\CategoryInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'ClawRock_Faq::category_save';

    private \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor;
    private \ClawRock\Faq\Api\CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \ClawRock\Faq\Api\CategoryRepositoryInterface $categoryRepository
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->categoryRepository = $categoryRepository;

        parent::__construct($context);
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface|null
     */
    public function execute(): ?ResultInterface
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->getRequest();
        $data = $request->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (empty($data[CategoryInterface::CATEGORY_ID])) {
                $data[CategoryInterface::CATEGORY_ID] = null;
            }

            $id = (int) $this->getRequest()->getParam(CategoryInterface::CATEGORY_ID);

            try {
                /** @var \ClawRock\Faq\Model\Category $model */
                $model = $id ? $this->categoryRepository->getById($id) : $this->categoryRepository->create();
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This category no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }
            $model->setData($data);

            $this->_eventManager->dispatch(
                'clawrock_faq_category_prepare_save',
                ['category' => $model, 'request' => $this->getRequest()]
            );
            try {
                $this->categoryRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the category.'));
                $this->dataPersistor->clear('clawrock_faq_category');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            CategoryInterface::CATEGORY_ID => $model->getId(),
                            '_current' => true,
                        ]
                    );
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                /** @var \Exception $e */
                $e = $e->getPrevious() ?: $e;
                $this->messageManager->addExceptionMessage($e);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the category.'));
            }
            $this->dataPersistor->set('clawrock_faq_category', $data);

            return $resultRedirect->setPath('*/*/edit', [
                CategoryInterface::CATEGORY_ID => $this->getRequest()->getParam(CategoryInterface::CATEGORY_ID),
            ]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
