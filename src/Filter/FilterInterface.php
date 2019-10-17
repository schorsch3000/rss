<?php


namespace Rss\Filter;


use Rss\Item;

interface FilterInterface
{
    public function setOptions($options);
    public function filter(Item $item);
}