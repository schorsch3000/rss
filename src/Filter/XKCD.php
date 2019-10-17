<?php

namespace Rss\Filter;

use Rss\Item;

class XKCD extends AbstractFilter
{
    public function filter(Item $item)
    {
        $html = $item->getContent();
        $doc = \phpQuery::newDocumentHTML($html);
        $html .= "\n<blockquote>";
        $html .= htmlentities($doc->find('img')->eq(0)->attr('title'));
        $html .= "</blockquote>";
        $item->setContent($html);
        return $item;
    }

}