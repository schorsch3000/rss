<?php

namespace Rss\Filter;

use Rss\Item;

class FixLinks extends AbstractFilter
{
    public function filter(Item $item)
    {


        $doc = \phpQuery::newDocumentHTML($item->getContent());

        foreach ($doc->find("img[src^=/]") as $img) {
            $img = pq($img);
            $img->attr('src',$this->rel2abs($img->attr('src'), $this->options['base']));
        }
        $item->setContent($doc->html());
        return $item;

    }

    private function rel2abs($rel, $base)
    {
        if (strpos($rel, "//") === 0) {
            return "http:" . $rel;
        }
        if (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;
        if ($rel[0] == '#' || $rel[0] == '?') return $base . $rel;
        $path='';
        $host='';
        $scheme='';
        extract(parse_url($base));
        $path = preg_replace('#/[^/]*$#', '', $path);
        if ($rel[0] == '/') $path = '';
        $abs = "$host$path/$rel";
        $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        for ($n = 1; $n > 0; $abs = preg_replace($re, '/', $abs, -1, $n));
        return $scheme . '://' . $abs;
    }

}