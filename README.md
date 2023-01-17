# FAQ

Magento 2 module, that allows you to create custom FAQ question & answers, place single and list of questions in CMS pages as widget. They can be filtered by category or searched by question.

### Compatibility
* Magento 2.4
* PHP 7.4, 8.1

### Installation
1. `composer require clawrock/magento2-faq`
2. `php bin/magento setup:upgrade`

### Configuration
1. Go to Stores -> Configuration -> General -> FAQ
2. Set default question limit displayed per page when display all questions using widget

### Widgets
You can add questions using widgets. There are three types of widgets:
1. Question list - display list of selected questions & answers
2. Question - display single question & answer
3. Faq - display all questions & answers with limit per page.

### Tests
To run test run from console: `vendor/bin/phpunit -c phpunit.xml.dist`

