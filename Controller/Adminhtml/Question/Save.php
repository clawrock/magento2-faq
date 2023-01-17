<?php
declare(strict_types=1);

namespace ClawRock\Faq\Controller\Adminhtml\Question;

use ClawRock\Faq\Api\Data\QuestionInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'ClawRock_Faq::question_save';

    private \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor;
    private \ClawRock\Faq\Api\QuestionRepositoryInterface $questionRepository;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \ClawRock\Faq\Api\QuestionRepositoryInterface $questionRepository
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->questionRepository = $questionRepository;

        parent::__construct($context);
    }

    /**
     * Save action
     *
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
            if (empty($data[QuestionInterface::QUESTION_ID])) {
                $data[QuestionInterface::QUESTION_ID] = null;
            }

            $id = (int) $this->getRequest()->getParam(QuestionInterface::QUESTION_ID);
            try {
                /** @var \ClawRock\Faq\Model\Question $model */
                $model = $id ? $this->questionRepository->getById($id) : $this->questionRepository->create();
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This question no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            $this->_eventManager->dispatch(
                'clawrock_faq_question_prepare_save',
                ['question' => $model, 'request' => $this->getRequest()]
            );

            try {
                $this->questionRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the question.'));
                $this->dataPersistor->clear('clawrock_faq_question');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['question_id' => $model->getId(), '_current' => true]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                /** @var \Exception|null $ep */
                $ep = $e->getPrevious();
                $this->messageManager->addExceptionMessage($ep ?: $e);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the question.'));
            }

            $this->dataPersistor->set('clawrock_faq_question', $data);

            return $resultRedirect->setPath('*/*/edit', [
                'question_id' => $this->getRequest()->getParam('question_id'),
            ]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
