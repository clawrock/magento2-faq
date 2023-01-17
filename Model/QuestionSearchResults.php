<?php
declare(strict_types=1);

namespace ClawRock\Faq\Model;

use ClawRock\Faq\Api\Data\QuestionSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

class QuestionSearchResults extends SearchResults implements QuestionSearchResultsInterface
{
}
