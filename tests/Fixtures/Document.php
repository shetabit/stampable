<?php

namespace Shetabit\Stampable\Tests\Fixtures;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Shetabit\Stampable\Traits\HasStamps;

/**
 * A model holding both a stamp and a stamp named after its negation.
 *
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $unpublished_at
 *
 * @method bool isPublished()
 * @method bool isUnpublished()
 * @method static Builder<Document> published()
 * @method static Builder<Document> unpublished()
 */
class Document extends Model
{
    use HasStamps;

    /**
     * @var array<string, string>
     */
    protected $stamps = [
        'published' => 'published_at',
        'unpublished' => 'unpublished_at',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = ['title', 'published_at', 'unpublished_at'];

    /**
     * @return array<string, string>
     */
    protected function casts() : array
    {
        return [
            'published_at' => 'datetime',
            'unpublished_at' => 'datetime',
        ];
    }
}
