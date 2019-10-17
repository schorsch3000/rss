<?php


namespace Rss\Filter;


abstract class AbstractFilter implements FilterInterface
{
    protected $options = [];

    public function setOptions($options)
    {
        foreach ((array)$options as $k => $v) {
            $this->options[$k] = $v;
        }
    }
}