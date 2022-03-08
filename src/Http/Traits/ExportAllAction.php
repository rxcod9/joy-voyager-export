<?php

namespace Joy\VoyagerExport\Http\Traits;

use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel;

trait ExportAllAction
{
    //***************************************
    //               ____
    //              |  _ \
    //              | |_) |
    //              |  _ <
    //              | |_) |
    //              |____/
    //
    //      Export DataTable our Data Type (B)READ
    //
    //****************************************

    public function exportAll(Request $request)
    {
        // Check permission
        $this->authorize('browse_bread');

        $writerType = $request->get('writerType', config('joy-voyager-export.writerType', Excel::XLSX));
        $fileName   = $request->get('fileName', ('export-all.' . Str::lower($writerType)));

        $exportClass = 'joy-voyager-export.export-all';

        $export = app($exportClass);

        return $export->set(
            $request->all(),
        )->download(
            $fileName,
            $writerType
        );
    }
}
