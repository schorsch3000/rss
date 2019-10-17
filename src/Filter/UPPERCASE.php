<?php


namespace Rss\Filter;


use Rss\Item;

class UPPERCASE extends AbstractFilter
{
    public function filter(Item $item)
    {
        $item->setContent(strtoupper($item->getContent()));
        return $item;
    }

}