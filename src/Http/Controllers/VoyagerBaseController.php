<?php

namespace Joy\VoyagerExport\Http\Controllers;

use Joy\VoyagerExport\Http\Traits\ExportAction;
use Joy\VoyagerExport\Http\Traits\ExportAllAction;
use Joy\VoyagerCore\Http\Controllers\VoyagerBaseController as BaseVoyagerBaseController;

class VoyagerBaseController extends BaseVoyagerBaseController
{
    use ExportAction;
    use ExportAllAction;
}
