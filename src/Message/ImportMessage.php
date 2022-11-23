<?php

namespace App\Message;


class ImportMessage
{


    /**
     * @var string
     */
    private $data;

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function __construct(string $data)
    {
        $this->data = $data;
    }


}