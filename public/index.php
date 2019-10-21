<?php
chdir(__DIR__ . '/../');
require "vendor/autoload.php";


/**
 *
 * minimize assets
 */

$assetDirs = [__DIR__ . '/js/*.js', __DIR__ . '/css/*.css'];
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

/**
 *  actual index
 */


$klein = new \Klein\Klein();

$klein->respond(function ($request, $response, $service, $app) use ($klein) {

    $app->register('pug', function () {
        @mkdir('./cache/pug', 0777, true);
        return new \Pug\Pug(['cache' => './cache/pug']);
    });
    $app->register('config', function () {
        return \Symfony\Component\Yaml\Yaml::parse(file_get_contents('./config.yaml'));
    });
});

$klein->respond('*', function (\Klein\Request $request, \Klein\Response $response, \Klein\ServiceProvider $service, \Klein\App $app) {
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        $response->header('WWW-Authenticate', 'Basic realm="My Realm"');
        $response->code(401);
        $response->send();
    } else {
        if ($_SERVER['PHP_AUTH_USER'] !== $app->config['user'] || md5($_SERVER['PHP_AUTH_PW']) !== $app->config['pass']) {
            $response->code(401);
            $response->send();
        }
    }

});


$klein->respond('GET', '/', function (\Klein\Request $request, \Klein\Response $response, $service, $app) {

    $items = [];
    foreach (glob('./public/data/*/*/NEW') as $newFile) {
        $dir = dirname($newFile);
        $dir = preg_replace("#^\\./public#", null, $dir);
        $dir .= '/';
        $items[] = $dir;
    }

    usort($items, function ($a, $b) {
        $aTime = file_get_contents("./public/" . $a . '/FIRST_SEEN');
        $bTime = file_get_contents("./public/" . $b . '/FIRST_SEEN');
        return $bTime - $aTime;
    });

    return $app->pug->render('./tpl/index.pug', ["items" => $items]);


});
$klein->respond('GET', '/markread/[:feedid]/[:itemid]/index.html', function (\Klein\Request $request, \Klein\Response $response, $service, $app) {
    $dataPath = "./public/data/" . $request->param('feedid') . '/' . $request->param('itemid');

    if (!is_dir($dataPath)) {
        return $response->code(404);
    }
    if (is_file($dataPath . '/NEW')) {
        unlink($dataPath . '/NEW');
    }
});
$klein->respond('GET', '/data/[:feedid]/[:itemid]/index.html', function (\Klein\Request $request, \Klein\Response $response, $service, $app) {

    $dataPath = "./public" . dirname($request->uri());
    if (!is_dir($dataPath)) {
        return $response->code(404);
    }
    $data = json_decode(file_get_contents($dataPath . '/data.json'), true);
    $data['NEW'] = is_file($dataPath . '/NEW');
    $data['NEW_INT'] = intval(is_file($dataPath . '/NEW'));
    $data['FIRST_SEEN'] = intval(file_get_contents($dataPath . '/FIRST_SEEN'));
    $data['itemurl'] = $request->uri();

    $html = $app->pug->render('./tpl/item.pug', $data);
    file_put_contents($dataPath . '/index.html', $html);
    return $html;
});
$klein->dispatch();
