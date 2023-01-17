<?php
declare(strict_types=1);

namespace ClawRock\Faq\Test\Unit\Model;

use ClawRock\Faq\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $scopeConfigMock;
    private \ClawRock\Faq\Model\Config $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->model = new Config($this->scopeConfigMock);
    }

    public function testGetDefaultQuestionLimit(): void
    {
        $this->scopeConfigMock->expects($this->once())->method('getValue')->with(
            'clawrock_faq/general/question_limit',
            ScopeInterface::SCOPE_STORE,
            null
        )->willReturn('5');

        $this->assertEquals(5, $this->model->getDefaultQuestionLimit());
    }
}
