<?php
/**
 * Laravel-Env-Sync
 *
 * @author Julien Tant - Craftyx <julien@craftyx.fr>
 */

namespace Poseidonphp\LaravelEnvSync\Tests\Console;


use Artisan;
use Poseidonphp\LaravelEnvSync\EnvSyncServiceProvider;
use Orchestra\Testbench\TestCase;
use org\bovigo\vfs\vfsStream;

class SyncCommandTest extends TestCase
{
    const EXAMPLE_ENV_STRING = "FOO=BAR\nBAR=BAZ\nBAZ=FOO";
    const EXPECTED_ENV_STRING = "FOO=BAR\nBAZ=FOO\nBAR=BAZ";
    const ENV_FILE = '/.env';
    const ENV_EXAMPLE_FILE = '/.env.example';
    const ENV_SYNC_COMMAND = "env:sync";

    protected function getPackageProviders($app)
    {
        return [EnvSyncServiceProvider::class];
    }

    /** @test */
    public function it_should_fill_the_env_file_from_env_example()
    {
        // Arrange
        $root = vfsStream::setup();
        $env = "FOO=BAR\nBAZ=FOO";

        file_put_contents($root->url() . self::ENV_EXAMPLE_FILE, self::EXAMPLE_ENV_STRING);
        file_put_contents($root->url() . self::ENV_FILE, $env);

        $this->app->setBasePath($root->url());

        // Act
        Artisan::call(self::ENV_SYNC_COMMAND, [
            '--no-interaction' => true,
        ]);

        // Assert
        $this->assertEquals(self::EXPECTED_ENV_STRING, file_get_contents($root->url() . self::ENV_FILE));
    }

    /** @test */
    public function it_should_work_in_reverse_mode()
    {
        // Arrange
        $root = vfsStream::setup();
        $example  = "FOO=BAR\nBAZ=FOO";

        file_put_contents($root->url() . self::ENV_EXAMPLE_FILE, $example);
        file_put_contents($root->url() . self::ENV_FILE, self::EXAMPLE_ENV_STRING);

        $this->app->setBasePath($root->url());

        // Act
        Artisan::call(self::ENV_SYNC_COMMAND, [
            '--no-interaction' => true,
            '--reverse' => true,
        ]);

        // Assert
        $this->assertEquals(self::EXPECTED_ENV_STRING, file_get_contents($root->url() . self::ENV_EXAMPLE_FILE));
    }


    /** @test */
    public function it_should_work_when_providing_src_and_dest()
    {
        // Arrange
        $root = vfsStream::setup();
        $env = "FOO=BAR\nBAZ=FOO";

        file_put_contents($root->url() . '/.foo', self::EXAMPLE_ENV_STRING);
        file_put_contents($root->url() . '/.bar', $env);

        $this->app->setBasePath($root->url());

        // Act
        Artisan::call(self::ENV_SYNC_COMMAND, [
            '--no-interaction' => true,
            '--src' => $root->url() .'/.foo',
            '--dest' => $root->url() .'/.bar'
        ]);

        // Assert
        $this->assertEquals(self::EXPECTED_ENV_STRING, file_get_contents($root->url() . '/.bar'));
    }
}
