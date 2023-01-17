<?php
declare(strict_types=1);

namespace ClawRock\Faq\Controller\Adminhtml\Question;

use ClawRock\Faq\Api\Data\QuestionInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;

class Delete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'ClawRock_Faq::question_delete';

    /**
     * @var \ClawRock\Faq\Api\QuestionRepositoryInterface
     */
    private \ClawRock\Faq\Api\QuestionRepositoryInterface $questionRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \ClawRock\Faq\Api\QuestionRepositoryInterface $questionRepository
    ) {
        parent::__construct($context);
        $this->questionRepository = $questionRepository;
    }

    public function execute(): ?ResultInterface
    {
        $id = (int) $this->getRequest()->getParam(QuestionInterface::QUESTION_ID);
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $this->questionRepository->delete($this->questionRepository->getById($id));
            $this->messageManager->addSuccessMessage(__('The question has been deleted.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('*/*/');
    }
}
