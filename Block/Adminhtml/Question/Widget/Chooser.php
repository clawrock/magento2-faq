<?php
declare(strict_types=1);

namespace ClawRock\Faq\Block\Adminhtml\Question\Widget;

use ClawRock\Faq\Api\Data\QuestionInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Widget\Block\Adminhtml\Widget\Chooser as WidgetChooser;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Chooser extends \Magento\Backend\Block\Widget\Grid\Extended
{
    public const MASS_ACTION_FORM_FIELD_NAME = 'in_questions';

    private \ClawRock\Faq\Api\QuestionRepositoryInterface $questionRepository;
    private \ClawRock\Faq\Model\ResourceModel\Question\CollectionFactory $collectionFactory;
    private \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \ClawRock\Faq\Api\QuestionRepositoryInterface $questionRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \ClawRock\Faq\Model\ResourceModel\Question\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->questionRepository = $questionRepository;
        $this->collectionFactory = $collectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setUseAjax(true);
        $this->setDefaultFilter(['chooser_active' => '1']); // @phpstan-ignore-line

        /* @var $collection \ClawRock\Faq\Model\ResourceModel\Question\CollectionFactory */
        $collection = $this->collectionFactory->create();
        $this->setCollection($collection);
    }

    public function prepareElementHtml(AbstractElement $element): AbstractElement
    {
        /** @var string $id */
        $id = $element->getId();
        $uniqId = $this->mathRandom->getUniqueHash($id);
        $sourceUrl = $this->getUrl('clawrock_faq/question_widget/chooser', [
            'uniq_id' => $uniqId,
            'is_multiselect' => $this->getIsMultiselect(),
        ]);

        /** @var \Magento\Backend\Block\Widget\Form\Element\Gallery $chooser */
        $chooser = $this->getLayout()->createBlock(WidgetChooser::class);
        $chooser->setElement($element)
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);

        $this->setChooserLabel($chooser, $element->getValue());

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    public function getRowClickCallback(): string
    {
        if (!$this->getIsMultiselect()) {
            return '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var questionTitle = trElement.down("td").next().innerHTML;
                var questionId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                ' .
                $this->getId() .
                '.setElementValue(questionId);
                ' .
                $this->getId() .
                '.setElementLabel(questionTitle);
                ' .
                $this->getId() .
                '.close();
            }
        ';
        }

        return '';
    }

    public function getGridUrl(): string
    {
        return $this->getUrl('clawrock_faq/question_widget/chooser', [
            '_current' => true,
            'is_multiselect' => $this->getIsMultiselect(),
        ]);
    }

    public function getMainButtonsHtml(): string
    {
        return parent::getMainButtonsHtml() . $this->getChildHtml('choose_button');
    }

    public function getAdditionalJavascript(): string
    {
        if (!$this->getIsMultiselect()) {
            return '';
        }

        $js = '

            $("{selectId}").hide();
            {gridJsObject}.preInitCallback = function() {
                $("{selectId}").hide();
            }

            {massActionjsObject}.removeLabel = function(label){
                var currentLabel =  {chooserJsObject}.getElementLabelText();
                currentLabel = currentLabel.replace("<li>"+label+"</li>", "");
                 {chooserJsObject}.setElementLabel(currentLabel);
            }

            {massActionjsObject}.addLabel = function(label){
                var currentLabel = {chooserJsObject}.getElementLabelText();
                if(currentLabel.search("ul") != -1){
                    currentLabel = currentLabel.replace("</ul>", "");
                    currentLabel = currentLabel.replace("<li>"+label+"</li>", "");
                }
                else{
                    currentLabel = "<ul>";
                }
                currentLabel = currentLabel +"<li>"+label+"</li></ul>";
                {chooserJsObject}.setElementLabel(currentLabel);
            }

            {massActionjsObject}.doChoose = function(node,e){
                var items = [];
                {massActionjsObject}.getCheckboxes().each(function(element, i){
                    var label = element.up("td").next().next().innerHTML;
                    label = label.replace(/^\s\s*/, "").replace(/\s\s*$/, "");
                    if (element.checked) {
                        items.push(element.value);
                        {massActionjsObject}.addLabel(label);
                    } else {
                        {massActionjsObject}.removeLabel(label);
                    }
                });
                {chooserJsObject}.setElementValue(items);
                {chooserJsObject}.close();
            }
        ';
        return str_replace([
            '{valueId}',
            '{gridJsObject}',
            '{massActionjsObject}',
            '{chooserJsObject}',
            '{selectId}',
        ], [
            $this->getValueFieldId(),
            $this->getJsObjectName(),
            $this->getMassActionJsObjectName(),
            $this->getId(),
            $this->getMassActionSelectId(),
        ], $js);
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'chooser_id',
            [
                'header' => __('ID'),
                'index' => 'question_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );

        $this->addColumn(
            'chooser_question',
            [
                'header' => __('Question'),
                'index' => 'question',
                'header_css_class' => 'col-title',
                'column_css_class' => 'col-title',
            ]
        );

        $this->addColumn(
            'chooser_answer',
            [
                'header' => __('Answer'),
                'index' => 'answer',
                'header_css_class' => 'col-url',
                'column_css_class' => 'col-url',
            ]
        );

        $this->addColumn(
            'chooser_active',
            [
                'header' => __('Status'),
                'index' => 'active',
                'type' => 'options',
                'options' => [1 => __('Enabled'), 0 => __('Disabled')],
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status',
            ]
        );

        return parent::_prepareColumns();
    }

    protected function _prepareLayout()
    {
        if ($this->getIsMultiselect()) {
            /** @var \Magento\Framework\View\Element\Template $block */
            $block = $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Button::class);
            $this->setChild(
                'choose_button',
                $block->setData([
                    'label' => __('Choose Selected Questions'),
                    'class' => 'action-secondary',
                    'onclick' => $this->getMassActionJsObjectName() . '.doChoose()',
                ])->setDataAttribute(['action' => 'grid-chooser-choose'])
            );
        }

        return parent::_prepareLayout();
    }

    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();
        if ($this->getIsMultiselect()) {
            $this->setMassactionIdField('question_id');
            $this->getMassactionBlock()
                ->setFormFieldName(self::MASS_ACTION_FORM_FIELD_NAME)
                ->setUseSelectAll(false);

            /** @var \Magento\Backend\Block\Widget\Grid\Massaction\Extended $el */
            $el = $this->getMassactionBlock();
            $el->addItem('mass_tohide', []);
        }
        return $this;
    }

    private function getIsMultiselect(): bool
    {
        return (bool) $this->getRequest()->getParam('is_multiselect', $this->getData('is_multiselect'));
    }

    private function setChooserLabel(WidgetChooser &$chooser, ?string $value): void
    {
        if (empty($value)) {
            return;
        }

        if ($this->getIsMultiselect()) {
            $questionIds = explode(',', $value);
        } else {
            $questionIds = [$value];
        }

        $label = '';

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(QuestionInterface::QUESTION_ID, $questionIds, 'in')
            ->create();

        $questions = $this->questionRepository->getList($searchCriteria)->getItems();
        foreach ($questions as $question) {
            $label .= "<li>{$this->_escaper->escapeHtml($question->getQuestion())}</li>"; // @phpstan-ignore-line
        }

        $chooser->setLabel($label);
    }

    private function getMassActionJsObjectName(): string
    {
        return str_replace('JsObject', '_massactionJsObject', $this->getJsObjectName());
    }

    private function getMassActionSelectId(): string
    {
        return str_replace('JsObject', '_massaction-select', $this->getJsObjectName());
    }

    private function getValueFieldId(): string
    {
        return str_replace('JsObject', 'value', $this->getJsObjectName());
    }
}
