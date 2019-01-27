<?php

namespace AutoMapperPlus\Test\Models\Issues\Issue33;

class User
{
    private $id;
    private $cellphone;
    private $phone;

    public function getId()
    {
        return $this->id;
    }

    public function getCellphone()
    {
        return $this->cellphone;
    }

    public function getPhone()
    {
        return $this->phone;
    }
}
