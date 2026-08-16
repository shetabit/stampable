<?php

namespace Shetabit\Stampable\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Shetabit\Stampable\Exceptions\StampNotFoundException;

interface Stampable
{
    /**
     * @return array<string, string>
     */
    public function getStamps() : array;

    public function hasStamp(string $stampName) : bool;

    /**
     * @throws StampNotFoundException
     */
    public function getStampField(string $stampName) : string;

    /**
     * @throws StampNotFoundException
     */
    public function isStampedBy(string $stampName) : bool;

    /**
     * @throws StampNotFoundException
     */
    public function isUnstampedBy(string $stampName) : bool;

    /**
     * @throws StampNotFoundException
     */
    public function markAsStamped(string $stampName) : bool;

    /**
     * @throws StampNotFoundException
     */
    public function markAsUnstamped(string $stampName) : bool;

    /**
     * @param  Builder<covariant Model>  $query
     * @return Builder<covariant Model>
     *
     * @throws StampNotFoundException
     */
    public function scopeStamped(Builder $query, string $stampName) : Builder;

    /**
     * @param  Builder<covariant Model>  $query
     * @return Builder<covariant Model>
     *
     * @throws StampNotFoundException
     */
    public function scopeUnstamped(Builder $query, string $stampName) : Builder;
}
