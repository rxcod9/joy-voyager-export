<?php

namespace Joy\VoyagerExport\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Joy\VoyagerExport\Notifications\Export;
use TCG\Voyager\Contracts\User;

class AsyncExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $export;
    public $path;
    public $url;
    public $disk;
    public $writerType;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        User $user,
        $export,
        $path,
        $url,
        $disk,
        $writerType
    ) {
        $this->user = $user;
        $this->export = $export;
        $this->path = $path;
        $this->url = $url;
        $this->disk = $disk;
        $this->writerType = $writerType;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->export->store($this->path, $this->disk, $this->writerType);

        $this->user->notify(new Export($this->path, $this->url));
    }
}
