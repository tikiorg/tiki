<?php
class Feed_Item
{
    public $origin;
    public $name;
    public $title;
    public $data;
    public $date;
    public $author;
    public $hits;
    public $importance;
    public $keywords;
    public $href;

    public function __construct($origin, $name, $title, $data, $date, $author, $hits, $importance, $keywords, $href)
    {
        $this->origin = $origin;
        $this->name = $name;
        $this->title = $title;
        $this->data = $data;
        $this->date = $date;
        $this->author = $author;
        $this->hits = $hits;
        $this->importance = $importance;
        $this->keywords = $keywords;
        $this->href = $href;
    }
}