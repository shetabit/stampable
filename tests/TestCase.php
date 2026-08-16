<?php

namespace Shetabit\Stampable\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Shetabit\Stampable\Tests\Fixtures\Article;

abstract class TestCase extends BaseTestCase
{
    /**
     * Define the environment the tests run in.
     *
     * @param  Application  $app
     */
    protected function defineEnvironment($app) : void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Run the migrations of the test models.
     */
    protected function defineDatabaseMigrations() : void
    {
        $this->loadMigrationsFrom(__DIR__.'/Fixtures/migrations');
    }

    /**
     * A saved article.
     *
     * @param array<string, mixed> $attributes
     */
    protected function createArticle(array $attributes = []) : Article
    {
        return Article::query()->create($attributes + ['title' => 'An article']);
    }
}
