<?php

namespace Shetabit\Stampable\Tests\Feature;

use Shetabit\Stampable\Tests\Fixtures\Article;
use Shetabit\Stampable\Tests\Fixtures\Document;
use Shetabit\Stampable\Tests\Fixtures\Post;
use Shetabit\Stampable\Tests\Fixtures\ReversedDocument;
use Shetabit\Stampable\Tests\TestCase;

class ScopesTest extends TestCase
{
    public function testItQueriesTheStampedRecords() : void
    {
        $published = $this->createArticle(['title' => 'Published', 'published_at' => '2019-01-09 12:00:00']);
        $this->createArticle(['title' => 'A draft']);

        $this->assertSame([$published->getKey()], Article::stamped('published')->pluck('id')->all());
    }

    public function testItQueriesTheUnstampedRecords() : void
    {
        $this->createArticle(['title' => 'Published', 'published_at' => '2019-01-09 12:00:00']);
        $draft = $this->createArticle(['title' => 'A draft']);

        $this->assertSame([$draft->getKey()], Article::unstamped('published')->pluck('id')->all());
    }

    public function testItQueriesTheStampedRecordsThroughTheDynamicScope() : void
    {
        $published = $this->createArticle(['title' => 'Published', 'published_at' => '2019-01-09 12:00:00']);
        $this->createArticle(['title' => 'A draft']);

        $this->assertSame([$published->getKey()], Article::published()->pluck('id')->all());
    }

    public function testItQueriesTheUnstampedRecordsThroughTheDynamicScope() : void
    {
        $this->createArticle(['title' => 'Published', 'published_at' => '2019-01-09 12:00:00']);
        $draft = $this->createArticle(['title' => 'A draft']);

        $this->assertSame([$draft->getKey()], Article::unpublished()->pluck('id')->all());
    }

    public function testTheDynamicScopesIgnoreTheCaseOfTheStampName() : void
    {
        $deleted = Post::query()->create(['title' => 'Deleted', 'soft_deleted_at' => '2019-01-09 12:00:00']);
        $kept = Post::query()->create(['title' => 'Kept']);

        $this->assertSame([$deleted->getKey()], Post::softDeleted()->pluck('id')->all());
        $this->assertSame([$kept->getKey()], Post::unSoftDeleted()->pluck('id')->all());
    }

    public function testADeclaredStampWinsOverTheNegationOfAnotherInAScope() : void
    {
        $unpublished = Document::query()->create([
            'title' => 'Withdrawn',
            'unpublished_at' => '2019-01-09 12:00:00',
        ]);
        Document::query()->create(['title' => 'Never published']);

        $this->assertSame([$unpublished->getKey()], Document::unpublished()->pluck('id')->all());
    }

    public function testTheOrderTheStampsAreDeclaredInDoesNotChangeWhatAScopeMeans() : void
    {
        $unpublished = Document::query()->create([
            'title' => 'Withdrawn',
            'unpublished_at' => '2019-01-09 12:00:00',
        ]);
        Document::query()->create(['title' => 'Never published']);

        $this->assertSame(
            [$unpublished->getKey()],
            ReversedDocument::unpublished()->pluck('id')->all(),
        );
    }

    public function testAScopeCanBeChainedWithTheRestOfTheQuery() : void
    {
        $this->createArticle(['title' => 'Published', 'published_at' => '2019-01-09 12:00:00']);
        $wanted = $this->createArticle(['title' => 'Wanted', 'published_at' => '2019-01-09 12:00:00']);

        $this->assertSame(
            [$wanted->getKey()],
            Article::published()->where('title', 'Wanted')->pluck('id')->all(),
        );
    }

    public function testTheScopesCanBeCombined() : void
    {
        $this->createArticle([
            'title' => 'Published and archived',
            'published_at' => '2019-01-09 12:00:00',
            'archived_at' => '2019-01-10 12:00:00',
        ]);
        $live = $this->createArticle(['title' => 'Live', 'published_at' => '2019-01-09 12:00:00']);

        $this->assertSame(
            [$live->getKey()],
            Article::stamped('published')->unstamped('archived')->pluck('id')->all(),
        );
    }

    public function testAScopeIsAvailableOnAnInstanceAsWell() : void
    {
        $published = $this->createArticle(['title' => 'Published', 'published_at' => '2019-01-09 12:00:00']);
        $article = $this->createArticle(['title' => 'A draft']);

        $this->assertSame([$published->getKey()], $article->published()->pluck('id')->all());
    }
}
