<?php

namespace Joy\VoyagerExport\Http\Traits;

use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Joy\VoyagerExport\Jobs\AsyncExport;
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

        $export->set(
            $dataType,
            $request->all(),
        );

        if (!$this->shouldExportAsync($export)) {
            return $export->download(
                $fileName,
                $writerType
            );
        }

        $disk = config('joy-voyager-export.disk');

        $path = 'public/exports/' . $dataType->slug . '-' . date('YmdHis') . '.' . Str::lower($writerType);

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
            'message'    => __('joy-voyager-export::generic.successfully_export_queued') . " {$dataType->getTranslatedAttribute('display_name_singular')}",
            'alert-type' => 'success',
        ]);
    }

    protected function shouldExportAsync($export)
    {
        if (config('joy-voyager-export.async', false) === true) {
            return true;
        }

        if (
            config('joy-voyager-export.auto_large_async', false) === true &&
            $export->query()->count() >= (int) config('joy-voyager-export.auto_large_async_max_number', 500)
        ) {
            return true;
        }

        return false;
    }
}
