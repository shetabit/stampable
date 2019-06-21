<?php

namespace Shetabit\Stampable\Traits;

trait HasStamps
{
    /**
     * Get current timestamp
     *
     * @return false|\Illuminate\Support\Carbon|string
     */
    private function getFreshTimestamp()
    {
        $timestamp = function_exists('now') ? now() : date('Y/m/d H:i:s');

        return $timestamp;
    }

    /**
     * Determine if an stamp exists
     *
     * @param $stampName
     * @return bool
     */
    private function stampExists($stampName)
    {
        $stamps = $this->getStamps();

        return isset($stamps[$stampName]);
    }

    /**
     * Retrieve available stamps
     *
     * @return array
     */
    public function getStamps()
    {
        $stamps = [];

        if (!empty($this->stamps)) {
            /*
             * Change structure to [stampName => fieldName]
             * if stampName is numeric, we use fieldName as stampName.
             */
            foreach ($this->stamps as $key => $stamp) {
                if (is_numeric($key)) {
                    $stamps[$stamp] = $stamp;
                } else {
                    $stamps[$key] = $stamp;
                }
            }
        }

        return $stamps;
    }

    /**
     * Determine if the data is published.
     *
     * @param $stampName
     * @return bool
     */
    public function isStampedBy($stampName)
    {
        $stamps = $this->getStamps();

        // if stamp exists, we check it for not being null, or will return false
        return $this->stampExists($stampName) ? ($this->{$stamps[$stampName]} !== null) : false;
    }

    /**
     * Determine if the data is unpublished.
     *
     * @param $stampName
     * @return bool
     */
    public function isUnstampedBy($stampName)
    {
        $stamps = $this->getStamps();

        // if stamp exists, we check it for being null, or will return false
        return $this->stampExists($stampName) ? ($this->{$stamps[$stampName]} == null) : false;
    }

    /**
     * Mark the current instance as stamped.
     *
     * @return bool
     */
    public function markAsStamped($stampName)
    {
        $stamps = $this->getStamps();


        return $this->forceFill([$stamps[$stampName] => $this->getFreshTimestamp()])->save();
    }

    /**
     * Mark the current instance as unstamped.
     *
     * @return bool
     */
    public function markAsUnstamped($stampName)
    {
        $stamps = $this->getStamps();

        return $this->forceFill([$stamps[$stampName] => null])->save();
    }

    /**
     * Get only stamped data.
     *
     * @return mixed
     */
    public function scopeStamped($query, $stampName)
    {
        $stamps = $this->getStamps();

        return $query->whereNotNull($stamps[$stampName]);
    }

    /**
     * Get only unstamped data.
     *
     * @param $query
     * @param $stampName
     * @return mixed
     */
    public function scopeUnstamped($query, $stampName)
    {
        $stamps = $this->getStamps();

        return $query->whereNull($stamps[$stampName]);
    }

    private function getStampBehavior($method)
    {
        $behavior = null;
        $stampName = null;
        $lowerCaseMethod = strtolower($method);

        $prefixes = [
            'is' => 'isStampedBy',
            'isUn' => 'isUnstampedBy',
            'markAs' => 'markAsStamped',
            'markAsUn' => 'markAsUnstamped'
        ];

        $stampsName = array_keys($this->getStamps());

        foreach ($stampsName as $key => $name) {
            foreach ($prefixes as $prefix => $methodName) {
                if (strtolower($prefix.$name) == $lowerCaseMethod) {
                    $behavior = $methodName;
                    $stampName = $name;
                    break;
                }
            }
        }

        return empty($behavior) ? false : [$behavior, $stampName];
    }

    private function getStampScope($method)
    {
        $scope = null;
        $lowerCaseMethod = strtolower($method);

        $stampKeys = array_keys($this->getStamps());

        foreach ($stampKeys as $key) {
            if ($lowerCaseMethod == $key) {
                $scope = 'stamped';
            } elseif ($lowerCaseMethod == strtolower('un'.$key)) {
                $scope = 'unstamped';
            }
        }

        return $scope;
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, ['increment', 'decrement'])) {
            return $this->$method(...$parameters);
        }

        if ($info = $this->getStampBehavior($method)) {
            $methodName = $info[0];
            $stampName = $info[1];
            return $this->$methodName($stampName);
        }

        if ($methodName = $this->getStampScope($method)) {
            return $this->forwardCallTo($this->newQuery(), $methodName, [$method]);
        }

        return $this->forwardCallTo($this->newQuery(), $method, $parameters);
    }
}
