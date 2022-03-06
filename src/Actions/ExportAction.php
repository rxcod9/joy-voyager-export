<?php

namespace Joy\VoyagerExport\Actions;

use Joy\VoyagerExport\Exports\DataTypeExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use TCG\Voyager\Actions\AbstractAction;
use TCG\Voyager\Facades\Voyager;
use Maatwebsite\Excel\Excel;

class ExportAction extends AbstractAction
{
    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
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
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-primary',
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

        return new DataTypeExport(
            $dataType,
            array_filter($ids),
            request()->all(),
            $this->writerType ?? config('joy-voyager-export.format', Excel::XLSX),
            $this->fileName ?? $this->dataType->slug
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
