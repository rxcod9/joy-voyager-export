<?php

namespace Joy\VoyagerExport\Events;

use Illuminate\Queue\SerializesModels;

class AllBreadDataExported
{
    use SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
