<?php

namespace Joy\VoyagerExport\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Joy\VoyagerExport\Exports\AllDataTypesExport as ExportsAllDataTypesExport;
use Maatwebsite\Excel\Excel;
use Symfony\Component\Console\Input\InputOption;

class AllDataTypesExport extends Command
{
    protected $name = 'joy-export:all';

    protected $description = 'Joy Voyager all DataTypes exporter';

    public function handle()
    {
        $this->output->title('Starting export');
        $this->exportAllDataTypes(
            $this->option('disk'),
            $this->option('writerType')
        );
        $this->output->success('Export successful');
    }

    protected function exportAllDataTypes(
        string $disk = null,
        string $writerType = Excel::XLSX
    ) {
        $path = 'public/exports/' . (($filePath ?? 'export-all') . '-' . date('YmdHis') . '.' . Str::lower($writerType));

        $url = config('app.url') . Storage::disk($disk)->url($path);

        $this->output->info(sprintf(
            'Exporting to >>' . PHP_EOL . 'path : %s' . PHP_EOL . 'url : %s',
            storage_path($path),
            $url
        ));

        (new ExportsAllDataTypesExport())->withOutput($this->output)->store(
            $path,
            $disk,
            $writerType
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'disk',
                'd',
                InputOption::VALUE_OPTIONAL,
                'The disk to where you want to export',
                config('joy-voyager-export.disk')
            ],
            [
                'writerType',
                'w',
                InputOption::VALUE_OPTIONAL,
                'The writerType in which format you want to export',
                config('joy-voyager-export.writerType', 'Xlsx')
            ],
        ];
    }
}
