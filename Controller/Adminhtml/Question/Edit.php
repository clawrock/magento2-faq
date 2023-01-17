<?php
declare(strict_types=1);

namespace ClawRock\Faq\Controller\Adminhtml\Question;

use ClawRock\Faq\Api\Data\QuestionInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'ClawRock_Faq::question_save';

    private \Magento\Framework\Registry $coreRegistry;
    private \ClawRock\Faq\Api\QuestionRepositoryInterface $questionRepository;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $registry,
        \ClawRock\Faq\Api\QuestionRepositoryInterface $questionRepository
    ) {
        $this->coreRegistry = $registry;
        $this->questionRepository = $questionRepository;

        parent::__construct($context);
    }

    public function execute(): ?ResultInterface
    {
        $id = (int) $this->getRequest()->getParam(QuestionInterface::QUESTION_ID);

        try {
            $question = $id ? $this->questionRepository->getById($id) : $this->questionRepository->create();
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This question no longer exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        $this->coreRegistry->register('clawrock_faq_question', $question);

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($resultPage instanceof \Magento\Backend\Model\View\Result\Page) {
            $resultPage->setActiveMenu('ClawRock_Faq::question')
                ->addBreadcrumb((string) __('FAQ'), (string) __('FAQ'))
                ->addBreadcrumb((string) __('Manage Questions'), (string) __('Manage Questions'))
                ->addBreadcrumb(
                    (string) ($id ? __('Edit Question') : __('New Question')),
                    (string) ($id ? __('Edit Question') : __('New Question'))
                );
        }
        $resultPage->getConfig()->getTitle()->prepend((string) __('Questions'));
        $resultPage->getConfig()->getTitle()
            ->prepend($question->getQuestionId() ? $question->getQuestion() : (string) __('New Question'));

        return $resultPage;
    }
}
