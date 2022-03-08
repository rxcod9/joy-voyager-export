<?php

namespace Joy\VoyagerExport\Http\Traits;

use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel;

trait ExportAction
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

    public function export(Request $request)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $writerType = $request->get('writerType', config('joy-voyager-export.writerType', Excel::XLSX));
        $fileName   = $request->get('fileName', ($dataType->slug . '.' . Str::lower($writerType)));

        $exportClass = 'joy-voyager-export.export';

        if (app()->bound('joy-voyager-export.' . $dataType->slug . '.export')) {
            $exportClass = 'joy-voyager-export.' . $dataType->slug . '.export';
        }

        $export = app($exportClass);

        return $export->set(
            $dataType,
            $request->all(),
        )->download(
            $fileName,
            $writerType
        );
    }
}
