#!/usr/bin/env php
<?php

use Symfony\Component\Yaml\Yaml;

ini_set('default_socket_timeout', 30);

chdir(__DIR__);
require "vendor/autoload.php";
$conf = Yaml::parse(file_get_contents("config.yaml"));

if ($_SERVER['argc'] === 1) {

    logInfo("Searching oldest feed");

    $oldestTime = time() - 60 * 60;
    $oldestName = false;
    foreach ($conf['feeds'] as $feedId => $feed) {
        if ($feed['lastFetched'] >= $oldestTime) {
            continue;
        }
        $oldestName = $feedId;
        $oldestTime = $feed['lastFetched'];
    }
    if (!$oldestName) {
        logInfo("no feed is older then 1h");
        exit;
    }

    $conf['feeds'][$oldestName]['lastFetched'] = time();
    file_put_contents('config.yaml', Yaml::dump($conf, 8, 2));
    $myFeed = $conf['feeds'][$oldestName];
    $myFeed['id'] = $oldestName;
} else {
    $id = $_SERVER['argv'][1];
    $myFeed = $conf['feeds'][$id];
    $myFeed['id'] = $id;
}


logInfo("Working on feed " . $myFeed['id']);


if (!preg_match("/^[0-9A-Za-z-_.]{3,128}$/", $myFeed['id'])) {
    echo $myFeed['id'], " is not a valid id\n";
    die(1);
}


$feed = new SimplePie();
$feed->cache = false;
$feed->set_feed_url($myFeed['url']);
logInfo("loading");
$feed->init();
logInfo("loaded");

$new = 0;
$feedDir = 'public/data/' . $myFeed['id'];
foreach ($feed->get_items() as $feedItem) {
    loginfo("  Working on item (title) " . $feedItem->get_title());
    $origId = $feedItem->get_id();
    $itemId = md5($origId);
    $itemDir = $feedDir . "/" . $itemId;
    if (is_dir($itemDir)) {
        touch($itemDir);
        $update = true;
    } else {
        $update = false;
        loginfo("    Creating directory");
        mkdir($itemDir, 0777, true);
        file_put_contents($itemDir . '/FIRST_SEEN', time());
    }


    $dataItem = new \Rss\Item();
    $dataItem->setUpdate($update ? time() : null);
    $dataItem->setTitle($feedItem->get_title());
    $dataItem->setContent($feedItem->get_content());
    $dataItem->setFeed($feed->get_title());
    $dataItem->setUrl($feedItem->get_link());
    $dataItem->setFeedId($myFeed['id']);
    if (isset($myFeed['filters']) && is_array($myFeed['filters'])) {
        logInfo("    Applying filters...");
        foreach ($myFeed['filters'] as $name => $options) {
            logInfo("      Applying filter $name");
            $filterClass = "Rss\\Filter\\$name";
            if (!class_exists($filterClass)) {
                logErr("      Filter $name does not exist");
                die(1);
            }
            $filter = new $filterClass;
            /** @var $filter \Rss\Filter\FilterInterface */
            print_r($options);
            $filter->setOptions($options);
            $dataItem = $filter->filter($dataItem);
        }
    } else {
        logInfo("    No filters set for this feed");
    }


    loginfo("    Writing Data");
    $fileContent = json_encode($dataItem, JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE);
    $jsonFile = $feedDir . "/$itemId/data.json";
    touch($jsonFile);
    if (file_get_contents($jsonFile) !== $fileContent) {
        file_put_contents($jsonFile, $fileContent);
        touch($feedDir . "/" . $itemId . '/NEW');
        @unlink("$feedDir/$itemId/index.html");
        $new++;
    }
    loginfo("    Done");

}


loginfo("Loaded $new new items");





function isTTY()
{
    return stream_isatty(STDERR);
}

function logErr()
{
    logit(...func_get_args());
}

function logInfo()
{
    if (isTTY())
        logit(...func_get_args());
}

function logit()
{
    foreach (func_get_args() as $arg) {
        echo date("[H:i:s] "), $arg, "\n";
    }
}

