<?php

namespace Shetabit\Stampable\Contracts;

interface Stampable
{
    /**
     * Retrieve available stamps
     *
     * @return array
     */
    public function getStamps();

    /**
     * Determine if the data is published.
     *
     * @param $stampName
     * @return mixed
     */
    public function isStampedBy($stampName);

    /**
     * Determine if the data is unpublished.
     *
     * @param $stampName
     * @return mixed
     */
    public function isUnstampedBy($stampName);

    /**
     * Mark the current instance as stamped.
     *
     * @param $stampName
     * @return mixed
     */
    public function markAsStamped($stampName);

    /**
     * Mark the current instance as unstamped.
     *
     * @return bool
     */
    public function markAsUnstamped($stampName);

    /**
     * Get only stamped data.
     *
     * @param $query
     * @param $stampName
     * @return mixed
     */
    public function scopeStamped($query, $stampName);

    /**
     * Get only unstamped data.
     *
     * @param $query
     * @param $stampName
     * @return mixed
     */
    public function scopeUnstamped($query, $stampName);
}
