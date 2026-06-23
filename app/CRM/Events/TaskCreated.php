<?php

namespace App\CRM\Events;

class TaskCreated
{
    public function __construct(
        public $task
    ) {}
}