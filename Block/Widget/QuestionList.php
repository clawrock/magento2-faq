<?php
declare(strict_types=1);

namespace ClawRock\Faq\Block\Widget;

use ClawRock\Faq\Api\Data\QuestionInterface;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class QuestionList extends Template implements BlockInterface
{
    private \ClawRock\Faq\Api\QuestionRepositoryInterface $questionRepository;
    private ?array $questions = null;
    private \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \ClawRock\Faq\Api\QuestionRepositoryInterface $questionRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->questionRepository = $questionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    protected function _construct()
    {
        parent::_construct();

        if (!$this->hasData('template')) {
            $this->setTemplate('widget/question/list.phtml');
        }
    }

    /**
     * @return \ClawRock\Faq\Api\Data\QuestionInterface[]
     */
    public function getQuestions(): array
    {
        if ($this->questions === null) {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(QuestionInterface::QUESTION_ID, $this->getData('question_ids'), 'in')
                ->create();

            $this->questions = $this->questionRepository->getList($searchCriteria)->getItems();
        }

        return $this->questions;
    }

    protected function _toHtml(): string
    {
        if (count($this->getQuestions())  === 0) {
            return '';
        }

        return parent::_toHtml();
    }
}
