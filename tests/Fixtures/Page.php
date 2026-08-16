<?php

namespace Shetabit\Stampable\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Shetabit\Stampable\Traits\HasStamps;

/**
 * A model that names its stamps after their columns and casts none of them.
 *
 * @property string $title
 * @property mixed $approved_at
 */
class Page extends Model
{
    use HasStamps;

    /**
     * @var list<string>
     */
    protected $stamps = ['approved_at'];

    /**
     * @var list<string>
     */
    protected $fillable = ['title', 'approved_at'];
}
