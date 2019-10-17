<?php

namespace Rss;

class Item implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $feedId='';

    /**
     * @return string
     */
    public function getFeedId()
    {
        return $this->feedId;
    }

    /**
     * @param string $feedId
     */
    public function setFeedId($feedId)
    {
        $this->feedId = $feedId;
    }
    /**
     * @var null|int
     */
    protected $update = null;
    /**
     * @var string
     */
    protected $title = '';
    /**
     * @var string
     */
    protected $content = '';
    /**
     * @var string
     */
    protected $feed = '';
    /**
     * @var string
     */
    protected $url = "";

    /**
     * @var string[]
     */
    protected $messages=[];


    public function getMessages(){
        return $this->messages;
    }

    public function addMessages($messages){
        foreach((array)$messages as $message){
            $this->messages[]=$message;
        }
    }

    /**
     * @return int|null
     */
    public function getUpdate()
    {
        return $this->update;
    }

    /**
     * @param int|null $update
     */
    public function setUpdate($update)
    {
        $this->update = $update;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * @param string $feed
     */
    public function setFeed($feed)
    {
        $this->feed = $feed;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function jsonSerialize()
    {
        return [
            "update" => $this->getUpdate(),
            "title" => $this->getTitle(),
            "content" => $this->getContent(),
            "feed" => $this->getFeed(),
            "url" => $this->getUrl(),
            "messages" => $this->getMessages(),
            "feedId" => $this->getFeedId()
        ];

    }

}
