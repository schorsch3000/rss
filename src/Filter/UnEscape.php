<?php

namespace Rss\Filter;

use Rss\Item;

class UnEscape extends AbstractFilter
{
    public function filter(Item $item)
    {
        $html = $item->getContent();
        $html=html_entity_decode($html);
        $item->setContent($html);
        return $item;
    }

}