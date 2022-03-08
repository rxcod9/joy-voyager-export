<?php

namespace Joy\VoyagerExport\Http\Controllers;

use Joy\VoyagerExport\Http\Traits\ExportAction;
use Joy\VoyagerExport\Http\Traits\ExportAllAction;
use TCG\Voyager\Http\Controllers\VoyagerBaseController as TCGVoyagerBaseController;

class VoyagerBaseController extends TCGVoyagerBaseController
{
    use ExportAction;
    use ExportAllAction;
}
