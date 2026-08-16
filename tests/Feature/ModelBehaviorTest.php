<?php

namespace Shetabit\Stampable\Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\PendingHasThroughRelationship;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ReflectionProperty;
use Shetabit\Stampable\Tests\Fixtures\Article;
use Shetabit\Stampable\Tests\Fixtures\Note;
use Shetabit\Stampable\Tests\Fixtures\User;
use Shetabit\Stampable\Tests\TestCase;

class ModelBehaviorTest extends TestCase
{
    protected function tearDown() : void
    {
        new ReflectionProperty(Model::class, 'relationResolvers')->setValue(null, []);

        parent::tearDown();
    }

    public function testTheModelStillResolvesARelationRegisteredAtRuntime() : void
    {
        Article::resolveRelationUsing(
            'writer',
            static fn (Article $article): BelongsTo => $article->belongsTo(User::class, 'author_id'),
        );

        $user = User::query()->create(['name' => 'Mahdi']);
        $article = $this->createArticle(['author_id' => $user->getKey()]);

        $this->assertTrue($user->is($article->writer()->first()));
    }

    public function testTheModelStillAnswersAThroughCall() : void
    {
        $this->assertInstanceOf(PendingHasThroughRelationship::class, new Article()->__call('throughAuthor', []));
    }

    public function testTheModelStillIncrementsAndDecrements() : void
    {
        $article = $this->createArticle(['views' => 10]);

        $article->increment('views', 5);
        $this->assertSame(15, $article->refresh()->views);

        $article->decrement('views', 3);
        $this->assertSame(12, $article->refresh()->views);
    }

    public function testTheModelStillIncrementsQuietly() : void
    {
        $article = $this->createArticle(['views' => 10]);
        $updatedAt = $article->updated_at;

        $article->incrementQuietly('views');

        $this->assertSame(11, $article->refresh()->views);
        $this->assertEquals($updatedAt, $article->updated_at);
    }

    public function testTheModelStillForwardsAnythingElseToItsQueryBuilder() : void
    {
        $this->createArticle(['title' => 'A draft']);

        $this->assertSame(1, new Article()->count());
    }

    public function testTheModelStillAnswersADynamicWhere() : void
    {
        $this->createArticle(['title' => 'Whatever']);
        $this->createArticle(['title' => 'Something else']);

        $this->assertSame(1, Article::whereTitle('Whatever')->count());
    }

    public function testAModelWithoutStampsBehavesLikeAPlainModel() : void
    {
        Note::query()->create(['title' => 'A note']);

        $this->assertSame(1, Note::whereTitle('A note')->count());
    }
}
