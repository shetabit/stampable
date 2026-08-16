<?php

namespace Shetabit\Stampable\Tests\Feature;

use Illuminate\Support\Carbon;
use Shetabit\Stampable\Tests\Fixtures\Article;
use Shetabit\Stampable\Tests\TestCase;

class StampingTest extends TestCase
{
    public function testItPressesAStampAndWritesItToTheDatabase() : void
    {
        $article = $this->createArticle();

        $this->assertTrue($article->markAsStamped('published'));

        $this->assertNotNull($article->refresh()->published_at);
    }

    public function testAPressedStampCarriesTheFreshTimestampOfTheModel() : void
    {
        Carbon::setTestNow('2019-01-09 12:00:00');

        $article = $this->createArticle();
        $article->markAsStamped('published');

        $this->assertSame('2019-01-09 12:00:00', $article->refresh()->published_at?->toDateTimeString());

        Carbon::setTestNow();
    }

    public function testItRemovesAStampAndWritesItToTheDatabase() : void
    {
        $article = $this->createArticle(['published_at' => '2019-01-09 12:00:00']);

        $this->assertTrue($article->markAsUnstamped('published'));

        $this->assertNull($article->refresh()->published_at);
    }

    public function testTheStampsOfAModelAreIndependentOfEachOther() : void
    {
        $article = $this->createArticle();

        $article->markAsStamped('published');

        $this->assertTrue($article->isStampedBy('published'));
        $this->assertTrue($article->isUnstampedBy('archived'));
    }

    public function testItPressesAStampThroughItsDynamicMethod() : void
    {
        $article = $this->createArticle();

        $article->markAsPublished();

        $this->assertTrue($article->refresh()->isPublished());
    }

    public function testItRemovesAStampThroughItsDynamicMethod() : void
    {
        $article = $this->createArticle(['published_at' => '2019-01-09 12:00:00']);

        $article->markAsUnpublished();

        $this->assertTrue($article->refresh()->isUnpublished());
    }

    public function testAStampSurvivesAReloadOfTheModel() : void
    {
        $article = $this->createArticle();
        $article->markAsPublished();

        $reloaded = Article::query()->whereKey($article->getKey())->firstOrFail();

        $this->assertTrue($reloaded->isPublished());
        $this->assertFalse($reloaded->isArchived());
    }
}
