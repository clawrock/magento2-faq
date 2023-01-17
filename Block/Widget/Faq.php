<?php
declare(strict_types=1);

namespace ClawRock\Faq\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Faq extends Template implements BlockInterface
{
    public const QUESTION_LIMIT_FIELD = 'question_limit';

    private \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder;
    private \ClawRock\Faq\Api\CategoryRepositoryInterface $categoryRepository;
    private \ClawRock\Faq\Api\QuestionRepositoryInterface $questionRepository;
    private \ClawRock\Faq\Model\Config $config;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \ClawRock\Faq\Api\CategoryRepositoryInterface $categoryRepository,
        \ClawRock\Faq\Api\QuestionRepositoryInterface $questionRepository,
        \ClawRock\Faq\Model\Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->categoryRepository = $categoryRepository;
        $this->questionRepository = $questionRepository;
        $this->config = $config;
    }

    protected function _construct()
    {
        parent::_construct();

        if (!$this->hasData('template')) {
            $this->setTemplate('widget/faq.phtml');
        }
    }

    /**
     * @return \ClawRock\Faq\Api\Data\CategoryInterface[]
     */
    public function getCategories(): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('current_store', true)
            ->addFilter('active', true)
            ->create();

        return $this->categoryRepository->getList($searchCriteria)->getItems();
    }

    /**
     * @param bool $limit
     * @return \ClawRock\Faq\Api\Data\QuestionInterface[]
     */
    public function getQuestions(bool $limit = true): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('current_store', true)
            ->addFilter('active', true)
            ->create();

        if ($limit) {
            $searchCriteria->setPageSize($this->getQuestionLimit());
        }

        return $this->questionRepository->getList($searchCriteria)->getItems();
    }

    public function getQuestionLimit(): int
    {
        if (!$this->hasData(self::QUESTION_LIMIT_FIELD)) {
            $this->setData(self::QUESTION_LIMIT_FIELD, $this->config->getDefaultQuestionLimit());
        }

        return (int) $this->getData(self::QUESTION_LIMIT_FIELD);
    }

    public function getQuestionsUrl(): string
    {
        return $this->getUrl('', ['_direct' => 'rest/V1/faq/questions']);
    }

    public function getPaginationLinks(): string
    {
        $links = '';
        $pages = ceil(count($this->getQuestions(false)) / $this->getQuestionLimit());

        if ($pages <= 1) {
            return $links;
        }

        for ($i = 1; $i <= $pages; $i++) {
            $links .= "<a href='#' data-page='" . $i . "'><span>" . $i . "</span></a>";
        }

        return $links;
    }
}
