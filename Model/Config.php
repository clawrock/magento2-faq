<?php
declare(strict_types=1);

namespace ClawRock\Faq\Model;

use Magento\Store\Model\ScopeInterface;

class Config
{
    public const QUESTION_SCROLL_PARAM = 'question';
    public const QUESTION_LIMIT = 'clawrock_faq/general/question_limit';

    private \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getDefaultQuestionLimit(?int $storeId = null): int
    {
        return (int) $this->scopeConfig->getValue(self::QUESTION_LIMIT, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
