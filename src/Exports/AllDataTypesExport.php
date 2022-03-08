<?php

namespace Joy\VoyagerExport\Exports;

// use App\Models\User;

use Illuminate\Console\OutputStyle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
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
        $sheets    = [];
        $dataTypes = Voyager::model('DataType')->get();

        foreach ($dataTypes as $dataType) {
            $exportClass = 'joy-voyager-export.export';

            if (app()->bound('joy-voyager-export.' . $dataType->slug . '.export')) {
                $exportClass = 'joy-voyager-export.' . $dataType->slug . '.export';
            }

            $export                                                           = app()->make($exportClass);
            $sheets[$dataType->getTranslatedAttribute('display_name_plural')] = $export->set($dataType);
        }

        return $sheets;
    }

    /**
     * @param  OutputStyle $output
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
