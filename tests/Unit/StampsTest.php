<?php

namespace Shetabit\Stampable\Tests\Unit;

use BadMethodCallException;
use Shetabit\Stampable\Exceptions\StampNotFoundException;
use Shetabit\Stampable\Tests\Fixtures\Article;
use Shetabit\Stampable\Tests\Fixtures\Document;
use Shetabit\Stampable\Tests\Fixtures\Note;
use Shetabit\Stampable\Tests\Fixtures\Page;
use Shetabit\Stampable\Tests\Fixtures\Post;
use Shetabit\Stampable\Tests\Fixtures\ReversedDocument;
use Shetabit\Stampable\Tests\TestCase;

class StampsTest extends TestCase
{
    public function testItReadsTheStampsOfTheModel() : void
    {
        $this->assertSame(
            ['published' => 'published_at', 'archived' => 'archived_at'],
            new Article()->getStamps(),
        );
    }

    public function testItNamesAStampAfterItsColumnWhenTheModelDeclaresAPlainList() : void
    {
        $this->assertSame(['approved_at' => 'approved_at'], new Page()->getStamps());
    }

    public function testItReportsNoStampsForAModelThatDeclaresNone() : void
    {
        $this->assertSame([], new Note()->getStamps());
    }

    public function testItKnowsWhichStampsItHas() : void
    {
        $article = new Article();

        $this->assertTrue($article->hasStamp('published'));
        $this->assertTrue($article->hasStamp('archived'));
        $this->assertFalse($article->hasStamp('reviewed'));
        $this->assertFalse($article->hasStamp('published_at'));
    }

    public function testItResolvesAStampToItsColumn() : void
    {
        $this->assertSame('published_at', new Article()->getStampField('published'));
    }

    public function testItRefusesToResolveAnUnknownStamp() : void
    {
        $this->expectException(StampNotFoundException::class);
        $this->expectExceptionMessage('The stamp [reviewed] is not defined. Available stamps: published, archived.');

        new Article()->getStampField('reviewed');
    }

    public function testItSaysWhenAModelHasNoStampAtAll() : void
    {
        $this->expectException(StampNotFoundException::class);
        $this->expectExceptionMessage('The stamp [published] is not defined. Available stamps: none.');

        new Note()->getStampField('published');
    }

    public function testItReadsAStampOffTheModel() : void
    {
        $article = new Article(['published_at' => '2019-01-09 12:00:00']);

        $this->assertTrue($article->isStampedBy('published'));
        $this->assertFalse($article->isUnstampedBy('published'));

        $this->assertFalse($article->isStampedBy('archived'));
        $this->assertTrue($article->isUnstampedBy('archived'));
    }

    public function testAStampIsEitherPressedOrNotPressed() : void
    {
        $page = new Page()->forceFill(['approved_at' => '']);

        $this->assertNotSame($page->isStampedBy('approved_at'), $page->isUnstampedBy('approved_at'));
    }

    public function testItRefusesToReadAnUnknownStamp() : void
    {
        $this->expectException(StampNotFoundException::class);

        new Article()->isStampedBy('reviewed');
    }

    public function testItRefusesToReadTheNegationOfAnUnknownStamp() : void
    {
        $this->expectException(StampNotFoundException::class);

        new Article()->isUnstampedBy('reviewed');
    }

    public function testItRefusesToPressAnUnknownStamp() : void
    {
        $this->expectException(StampNotFoundException::class);

        new Article(['title' => 'An article'])->markAsStamped('reviewed');
    }

    public function testItRefusesToRemoveAnUnknownStamp() : void
    {
        $this->expectException(StampNotFoundException::class);

        new Article(['title' => 'An article'])->markAsUnstamped('reviewed');
    }

    public function testItRefusesToScopeOnAnUnknownStamp() : void
    {
        $this->expectException(StampNotFoundException::class);

        new Article()->scopeStamped(Article::query(), 'reviewed');
    }

    public function testItRefusesToScopeOnTheNegationOfAnUnknownStamp() : void
    {
        $this->expectException(StampNotFoundException::class);

        new Article()->scopeUnstamped(Article::query(), 'reviewed');
    }

    public function testItReadsAStampThroughItsDynamicMethod() : void
    {
        $article = new Article(['published_at' => '2019-01-09 12:00:00']);

        $this->assertTrue($article->isPublished());
        $this->assertFalse($article->isUnpublished());

        $this->assertFalse($article->isArchived());
        $this->assertTrue($article->isUnarchived());
    }

    public function testTheDynamicMethodsIgnoreTheCaseOfTheStampName() : void
    {
        $post = new Post(['soft_deleted_at' => '2019-01-09 12:00:00']);

        $this->assertTrue($post->isSoftDeleted());
        $this->assertFalse($post->isUnSoftDeleted());
    }

    public function testADeclaredStampWinsOverTheNegationOfAnother() : void
    {
        $document = new Document(['unpublished_at' => '2019-01-09 12:00:00']);

        $this->assertTrue($document->isUnpublished());
        $this->assertFalse($document->isPublished());
    }

    public function testTheOrderTheStampsAreDeclaredInDoesNotChangeWhatAMethodMeans() : void
    {
        $this->assertFalse(new Document()->isUnpublished());
        $this->assertFalse(new ReversedDocument()->isUnpublished());
    }

    public function testItLeavesAMethodItDoesNotRecogniseToTheModel() : void
    {
        $this->expectException(BadMethodCallException::class);

        new Article()->__call('thereIsNoSuchMethod', []);
    }
}
