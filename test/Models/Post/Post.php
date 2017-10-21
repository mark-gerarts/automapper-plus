<?php

namespace AutoMapperPlus\Test\Models\Post;

class Post
{
    private $id;

    private $title;

    private $body;

    function __construct($id, $title, $body)
    {
        $this->id = $id;
        $this->title = $title;
        $this->body = $body;
    }
}
