<?php

use App\CRM\Providers\CrmServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\EventServiceProvider;

return [
    AppServiceProvider::class,
    EventServiceProvider::class,
    CrmServiceProvider::class,
];