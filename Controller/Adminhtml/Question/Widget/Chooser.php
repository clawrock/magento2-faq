<?php
declare(strict_types=1);

namespace ClawRock\Faq\Controller\Adminhtml\Question\Widget;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Chooser extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'Magento_Widget::widget_instance';

    private \Magento\Framework\View\LayoutInterface $layout;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        parent::__construct($context);
        $this->layout = $layout;
    }

    public function execute(): ?ResultInterface
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $isMultipleSelection = $this->getRequest()->getParam('is_multiselect');
        if ($isMultipleSelection) {
            $this->getRequest()->setParams([
                'internal_' . \ClawRock\Faq\Block\Adminhtml\Question\Widget\Chooser::MASS_ACTION_FORM_FIELD_NAME
                => $this->getRequest()->getParam('element_value'),
            ]);
        }

        $questionsGrid = $this->layout->createBlock(
            \ClawRock\Faq\Block\Adminhtml\Question\Widget\Chooser::class,
            '',
            ['data' => [
                'id' => $uniqId,
                'is_multiselect' => $isMultipleSelection,
            ]]
        );
        $html = $questionsGrid->toHtml();
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);

        return $resultRaw->setContents($html);
    }
}
