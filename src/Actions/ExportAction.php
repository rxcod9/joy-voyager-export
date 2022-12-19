<?php

namespace Joy\VoyagerExport\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Joy\VoyagerExport\Jobs\AsyncExport;
use TCG\Voyager\Actions\AbstractAction;
use TCG\Voyager\Facades\Voyager;
use Maatwebsite\Excel\Excel;

class ExportAction extends AbstractAction
{
    /**
     * Optional File Name
     */
    protected $fileName;

    /**
     * Optional Writer Type
     */
    protected $writerType;

    public function getTitle()
    {
        return __('joy-voyager-export::generic.bulk_export');
    }

    public function getIcon()
    {
        return 'voyager-download';
    }

    public function getPolicy()
    {
        return 'browse';
    }

    public function getAttributes()
    {
        return [
            'id'     => 'bulk_export_btn',
            'class'  => 'btn btn-primary',
            'target' => '_blank',
        ];
    }

    public function getDefaultRoute()
    {
        // return route('my.route');
    }

    public function shouldActionDisplayOnDataType()
    {
        return config('joy-voyager-export.enabled', true) !== false
            && isInPatterns(
                $this->dataType->slug,
                config('joy-voyager-export.allowed_slugs', ['*'])
            )
            && !isInPatterns(
                $this->dataType->slug,
                config('joy-voyager-export.not_allowed_slugs', [])
            );
    }

    public function massAction($ids, $comingFrom)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug(request());

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        Gate::authorize('browse', app($dataType->model_name));

        $writerType = request()->get('writerType', $this->writerType ?? config('joy-voyager-export.writerType', Excel::XLSX));
        $fileName   = $this->fileName ?? ($dataType->slug . '.' . Str::lower($writerType));

        $exportClass = 'joy-voyager-export.export';

        if (app()->bound("joy-voyager-export.$slug.export")) {
            $exportClass = "joy-voyager-export.$slug.export";
        }

        $export = app($exportClass);

        $export->set(
            $dataType,
            array_filter($ids),
            request()->all(),
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

        return redirect($comingFrom)->with([
            'message'    => __('joy-voyager-export::generic.successfully_export_queued') . " {$dataType->getTranslatedAttribute('display_name_singular')}",
            'alert-type' => 'success',
        ]);
    }

    public function view()
    {
        $view = 'joy-voyager-export::bread.export';

        if (view()->exists('joy-voyager-export::' . $this->dataType->slug . '.export')) {
            $view = 'joy-voyager-export::' . $this->dataType->slug . '.export';
        }
        return $view;
    }

    protected function getSlug(Request $request)
    {
        if (isset($this->slug)) {
            $slug = $this->slug;
        } else {
            $slug = explode('.', $request->route()->getName())[1];
        }

        return $slug;
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
