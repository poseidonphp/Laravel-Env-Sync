<?php
/**
 * Laravel-Env-Sync
 *
 * @author Julien Tant - Craftyx <julien@craftyx.fr>
 */

namespace Poseidonphp\LaravelEnvSync;

use Illuminate\Support\ServiceProvider;
use Poseidonphp\LaravelEnvSync\Reader\File\EnvFileReader;
use Poseidonphp\LaravelEnvSync\Reader\ReaderInterface;
use Poseidonphp\LaravelEnvSync\Writer\File\EnvFileWriter;
use Poseidonphp\LaravelEnvSync\Writer\WriterInterface;

class EnvSyncServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // bindings
        $this->app->bind(ReaderInterface::class, EnvFileReader::class);
        $this->app->bind(WriterInterface::class, EnvFileWriter::class);

        // artisan command
        $this->commands(Console\SyncCommand::class);
        $this->commands(Console\CheckCommand::class);
        $this->commands(Console\DiffCommand::class);
    }

    public function provides()
    {
        return [
            ReaderInterface::class,
            WriterInterface::class,
        ];
    }


}
