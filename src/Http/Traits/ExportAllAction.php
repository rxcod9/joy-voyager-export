<?php

namespace Joy\VoyagerExport\Http\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Joy\VoyagerExport\Jobs\AsyncExport;
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

        $export->set(
            request()->all(),
        );

        if (!$this->shouldExportAllAsync($export)) {
            return $export->download(
                $fileName,
                $writerType
            );
        }

        $disk = config('joy-voyager-export.disk');

        $path = 'public/exports/export-all-' . date('YmdHis') . '.' . Str::lower($writerType);

        $url = config('app.url') . Storage::disk($disk)->url($path);

        AsyncExport::dispatch(
            request()->user(),
            $export,
            $path,
            $url,
            $disk,
            $writerType
        );

        return redirect()->back()->with([
            'message'    => __('joy-voyager-export::generic.successfully_export_queued_all'),
            'alert-type' => 'success',
        ]);
    }

    protected function shouldExportAllAsync($export)
    {
        return config('joy-voyager-export.all_async', false) === true;
    }
}
