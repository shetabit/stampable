<?php

namespace Shetabit\Stampable\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 */
class User extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = ['name'];
}
