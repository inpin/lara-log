<?php

namespace Tests;

use Faker\Generator;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inpin\LaraLog\CaptureApiActionMiddleware;
use Inpin\LaraLog\LaraLogServiceProvider;
use Jenssegers\Mongodb\MongodbServiceProvider;
use Orchestra\Database\ConsoleServiceProvider;
use Orchestra\Testbench\TestCase;

class CommonTest extends TestCase
{
    /**
     * @var Generator
     */
    protected $faker;
    /**
     * @var CaptureApiActionMiddleware
     */
    private $captureApiActionMiddleware;
    /**
     * @var User
     */
    private $user;

    public function setUp()
    {
        parent::setUp();

        $this->captureApiActionMiddleware = new CaptureApiActionMiddleware($this->app);

        $this->artisan('migrate', [
            '--database' => 'mongodb',
            '--realpath' => realpath(__DIR__ . '/../migrations'),
        ]);
        $this->loadLaravelMigrations(['--database' => 'testbench']);

        $this->faker = resolve(Generator::class);

        User::unguard();
        $this->user = User::query()->create([
            'email' => $this->faker->unique()->email,
            'name' => $this->faker->name,
            'password' => bcrypt($this->faker->password),
        ]);
        User::reguard();

        $this->actingAs($this->user, 'api');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            LaraLogServiceProvider::class,
            ConsoleServiceProvider::class,
            MongodbServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'mongodb');
        $app['config']->set('database.connections.mongodb', [
            'driver' => 'mongodb',
            'host' => env('MONGO_HOST', '127.0.0.1'),
            'port' => env('MONGO_PORT', 27017),
            'database' => env('MONGO_DATABASE', 'inpin'),
            'options' => [
                'database' => env('MONGO_DATABASE', 'inpin') // sets the authentication database required by mongo 3
            ]
        ]);
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /** @test */
    public function a_api_call_is_captured()
    {
        $_SERVER['REQUEST_URI'] = '/abc';
        $_SERVER['SERVER_ADDR'] = '127.0.0.1';
        $_SERVER['SERVER_PORT'] = '80';
        $_SERVER['SERVER_NAME'] = 'localhost';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $request = Request::capture();
        $request->replace([
            'field1' => 'value1',
            'field2' => 'value2',
        ]);

        $this->captureApiActionMiddleware->handle($request, function () {
            return (new Response())->setContent('<html></html>');
        });
        $this->assertDatabaseHas('lara_log_api_action_logs', [
            'uri' => 'http://localhost/abc',
            'body' => json_encode([
                'field1' => 'value1',
                'field2' => 'value2',
            ]),
            'method' => 'POST'
        ], 'mongodb');
    }
}
