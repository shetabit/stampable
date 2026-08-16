<?php

namespace Shetabit\Stampable\Tests\Fixtures;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Shetabit\Stampable\Contracts\Stampable;
use Shetabit\Stampable\Traits\HasStamps;

/**
 * A model with two stamps, the way an application would declare one.
 *
 * @property string $title
 * @property int $views
 * @property Carbon|null $published_at
 * @property Carbon|null $archived_at
 * @property Carbon|null $updated_at
 *
 * @method bool isPublished()
 * @method bool isUnpublished()
 * @method bool isArchived()
 * @method bool isUnarchived()
 * @method bool markAsPublished()
 * @method bool markAsUnpublished()
 * @method bool incrementQuietly(string $column, int $amount = 1)
 * @method BelongsTo<User, $this> writer()
 * @method static Builder<Article> published()
 * @method static Builder<Article> unpublished()
 * @method static Builder<Article> archived()
 * @method static Builder<Article> unarchived()
 */
class Article extends Model implements Stampable
{
    use HasStamps;

    /**
     * @var array<string, string>
     */
    protected $stamps = [
        'published' => 'published_at',
        'archived' => 'archived_at',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = ['title', 'author_id', 'views', 'published_at', 'archived_at'];

    /**
     * @return BelongsTo<User, $this>
     */
    public function author() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts() : array
    {
        return [
            'published_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }
}
