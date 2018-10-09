<?php

namespace AutoMapperPlus\Test\Models\SimilarPropertyNames;

class Source
{
    private $second_id;

    private $id;

    public function __construct($id, $second_id)
    {
        $this->id = $id;
        $this->second_id = $second_id;
    }
}
