<?php

namespace Laurel\MultiRoute\App\Console\Commands;

use Illuminate\Console\Command;
use Laurel\MultiRoute\App\Models\Path;
use Laurel\MultiRoute\MultiRoute;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Command for clear cache of paths
 *
 * Class MultiRouteClearCache
 * @package Laurel\MultiRoute\App\Console\Commands
 */
class MultiRouteClearCache extends Command
{
    /**
     * Variable, which contains instance of progress bar
     *
     * @var ProgressBar
     */
    private $progressBar;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laurel/multi-route:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear paths cache';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!config('multi-route.use_cache')) {
            $this->warn('Cache is disabled. Enable it in the MultiRoute config.');
            return;
        }

        $this->process();
    }

    /**
     * Gets quantity of all paths. After that loads chunks with 50 items and clear cache for each of them.
     */
    private function process()
    {
        MultiRoute::clearCache();
        $this->info("\nCache has been cleared.");
    }
}
