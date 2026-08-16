<?php

namespace Shetabit\Stampable\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Shetabit\Stampable\Traits\HasStamps;

/**
 * A model that uses the trait without declaring any stamp.
 *
 * @property string $title
 */
class Note extends Model
{
    use HasStamps;

    /**
     * @var list<string>
     */
    protected $fillable = ['title'];
}
