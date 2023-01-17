<?php
declare(strict_types=1);

namespace ClawRock\Faq\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class Category extends Column
{
    private ?array $categories = null;
    private \ClawRock\Faq\Model\OptionSource\CategoryOptionSource $categoryOptionSource;
    private string $categoriesKey;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \ClawRock\Faq\Model\OptionSource\CategoryOptionSource $categoryOptionSource,
        array $components = [],
        array $data = [],
        string $categoriesKey = 'category_id'
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->categoryOptionSource = $categoryOptionSource;
        $this->categoriesKey = $categoriesKey;
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    private function prepareItem(array $item): string
    {
        if (empty($item[$this->categoriesKey])) {
            return '';
        }

        $categoryIds = $item[$this->categoriesKey];
        if (!is_array($categoryIds)) {
            $categoryIds = [$categoryIds];
        }

        $result = [];
        $categories = $this->getCategories();
        foreach ($categoryIds as $categoryId) {
            if (!isset($categories[$categoryId])) {
                continue;
            }
            $result[] = $categories[$categoryId];
        }

        return implode(', ', $result);
    }

    private function getCategories(): array
    {
        if ($this->categories === null) {
            $this->categories = [];
            foreach ($this->categoryOptionSource->toOptionArray() as $option) {
                $this->categories[$option['value']] = $option['label'];
            }
        }

        return $this->categories;
    }
}
