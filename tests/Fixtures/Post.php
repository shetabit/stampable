<?php

namespace Shetabit\Stampable\Tests\Fixtures;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Shetabit\Stampable\Traits\HasStamps;

/**
 * A model whose stamp is not spelled the way its dynamic methods are.
 *
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $soft_deleted_at
 *
 * @method bool isSoftDeleted()
 * @method bool isUnSoftDeleted()
 * @method static Builder<Post> softDeleted()
 * @method static Builder<Post> unSoftDeleted()
 */
class Post extends Model
{
    use HasStamps;

    /**
     * @var array<string, string>
     */
    protected $stamps = ['softDeleted' => 'soft_deleted_at'];

    /**
     * @var list<string>
     */
    protected $fillable = ['title', 'soft_deleted_at'];

    /**
     * @return array<string, string>
     */
    protected function casts() : array
    {
        return ['soft_deleted_at' => 'datetime'];
    }
}
