<?php

namespace Shetabit\Stampable\Tests\Fixtures;

/**
 * The same two stamps as `Document`, declared the other way round.
 */
class ReversedDocument extends Document
{
    /**
     * @var array<string, string>
     */
    protected $stamps = [
        'unpublished' => 'unpublished_at',
        'published' => 'published_at',
    ];

    protected $table = 'documents';
}
