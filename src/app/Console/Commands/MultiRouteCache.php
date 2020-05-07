<?php

namespace Laurel\MultiRoute\App\Console\Commands;

use Illuminate\Console\Command;
use Laurel\MultiRoute\App\Models\Path;
use Laurel\MultiRoute\MultiRoute;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Command for caching all paths
 *
 * Class MultiRouteCache
 * @package Laurel\MultiRoute\App\Console\Commands
 */
class MultiRouteCache extends Command
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
    protected $signature = 'laurel/multi-route:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Caches all paths';

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
     * Clears MultiRoute cache. Then, gets quantity of all paths. After that loads chunks with 50 items and adds cache for each of them.
     */
    private function process()
    {
        MultiRoute::clearCache();
        $pathsCount = Path::count();
        $this->progressBar = $this->getOutput()->createProgressBar($pathsCount);
        $this->progressBar->start();

        Path::chunk(50, function($paths) {
            foreach ($paths as $path) {
                $path->saveToCache();
                $this->progressBar->advance();
            }
        });

        $this->progressBar->finish();
        $this->info("\nAll paths have been added to cache.");
    }
}
