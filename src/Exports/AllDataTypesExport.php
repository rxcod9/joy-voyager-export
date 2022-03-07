<?php

namespace Joy\VoyagerExport\Exports;

// use App\Models\User;

use Illuminate\Console\OutputStyle;
use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Http\Controllers\Traits\BreadRelationshipParser;
use TCG\Voyager\Models\DataType;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use TCG\Voyager\Facades\Voyager;

class AllDataTypesExport implements
    WithMultipleSheets
{
    use Exportable;

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        $dataTypes = Voyager::model('DataType')->get();

        foreach ($dataTypes as $dataType) {
            $sheets[] = new DataTypeExport($dataType);
        }

        return $sheets;
    }

    /**
     * @param  OutputStyle  $output
     * @return $this
     */
    public function withOutput(OutputStyle $output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @return OutputStyle
     */
    public function getConsoleOutput(): OutputStyle
    {
        if (!$this->output instanceof OutputStyle) {
            $this->output = new OutputStyle(new StringInput(''), new NullOutput());
        }

        return $this->output;
    }
}
