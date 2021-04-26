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

class CheckCommandTest extends TestCase
{
    const EXAMPLE_ENV_STRING = "FOO=BAR\nBAR=BAZ\nBAZ=FOO";
    const ENV_FILE = '/.env';
    const ENV_EXAMPLE_FILE = '/.env.example';
    const ENV_CHECK_COMMAND = "env:check";

    protected function getPackageProviders($app)
    {
        return [EnvSyncServiceProvider::class];
    }

    /** @test */
    public function it_should_retun_0_when_keys_are_in_both_files()
    {
        // Arrange
        $root = vfsStream::setup();
        $env = "BAR=BAZ\nFOO=BAR\nBAZ=FOO";

        file_put_contents($root->url() . self::ENV_EXAMPLE_FILE, self::EXAMPLE_ENV_STRING);
        file_put_contents($root->url() . self::ENV_FILE, $env);


        $this->app->setBasePath($root->url());

        // Act
        $returnCode = Artisan::call(self::ENV_CHECK_COMMAND);

        // Assert
        $this->assertSame(0, (int)$returnCode);
    }


    /** @test */
    public function it_should_retun_1_when_files_are_different()
    {
        // Arrange
        $root = vfsStream::setup();
        $env = "FOO=BAR\nBAZ=FOO";

        file_put_contents($root->url() . self::ENV_EXAMPLE_FILE, self::EXAMPLE_ENV_STRING);
        file_put_contents($root->url() . self::ENV_FILE, $env);

        $this->app->setBasePath($root->url());

        // Act
        $returnCode = Artisan::call(self::ENV_CHECK_COMMAND);

        // Assert
        $this->assertSame(1, (int)$returnCode);
    }


    /** @test */
    public function it_should_work_in_reverse_mode()
    {
        // Arrange
        $root = vfsStream::setup();
        $example = "FOO=BAR\nBAZ=FOO";

        file_put_contents($root->url() . self::ENV_EXAMPLE_FILE, $example);
        file_put_contents($root->url() . self::ENV_FILE, self::EXAMPLE_ENV_STRING);

        $this->app->setBasePath($root->url());

        // Act
        $returnCode = Artisan::call(self::ENV_CHECK_COMMAND, ["--reverse" => true]);

        // Assert
        $this->assertSame(1, (int)$returnCode);
    }
}
