<?php

namespace Joy\VoyagerExport\Actions;

use Joy\VoyagerExport\Exports\DataTypeExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
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
        return 'Export';
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
            'class' => 'btn btn-sm btn-primary',
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

        $writerType = $this->writerType ?? config('joy-voyager-export.writerType', Excel::XLSX);
        $fileName   = $this->fileName ?? ($dataType->slug . '.' . Str::lower($writerType));

        $exportClass = 'joy-voyager-export.export';

        if (app()->bound("joy-voyager-export.$slug.export")) {
            $exportClass = "joy-voyager-export.$slug.export";
        }

        $export = app($exportClass);

        return $export->set(
            $dataType,
            array_filter($ids),
            request()->all(),
        )->download(
            $fileName,
            $writerType
        );
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
}
