<?php
declare(strict_types=1);

namespace ClawRock\Faq\Block\Widget;

use ClawRock\Faq\Api\Data\QuestionInterface;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Question extends Template implements BlockInterface
{
    private \ClawRock\Faq\Api\QuestionRepositoryInterface $questionRepository;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \ClawRock\Faq\Api\QuestionRepositoryInterface $questionRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->questionRepository = $questionRepository;
    }

    protected function _construct()
    {
        parent::_construct();

        if (!$this->hasData('template')) {
            $this->setTemplate('widget/question/default.phtml');
        }
    }

    public function getQuestion(): QuestionInterface
    {
        return $this->questionRepository->getById((int) $this->getData('question_id'));
    }
}
