<?php

namespace Rss\Filter;

use andreskrey\Readability\Configuration;
use andreskrey\Readability\ParseException;
use Rss\Item;

class Readability extends AbstractFilter
{
    public function filter(Item $item)
    {
        $readability = new \andreskrey\Readability\Readability(new Configuration());
        try {
            $readability->parse($item->getContent());
            $item->setContent((string)$readability);
        } catch (ParseException $e) {
            $item->addMessages(sprintf('Error processing text: %s', $e->getMessage()));
        }
        return $item;
    }

}