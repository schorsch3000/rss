<?php
$assetDirs = [__DIR__ . '/public/js/*.js', __DIR__ . '/public/css/*.css'];
foreach ($assetDirs as $assetDir) {
    foreach (glob($assetDir) as $assetSrc) {
        if (strpos($assetSrc, '.min.')) continue;
        $assetDest = preg_replace('/(\.[^.]*)$/', '.min$1', $assetSrc);
        if (!is_file($assetDest) || filemtime($assetDest) < filemtime($assetSrc)) {
            if (substr($assetSrc, -2) === 'js') {
                $min = new MatthiasMullie\Minify\JS($assetSrc);
            } else {
                $min = new MatthiasMullie\Minify\CSS($assetSrc);
            }
            $min->minify($assetDest);
        }
    }
}
