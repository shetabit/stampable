<?php

namespace Shetabit\Stampable\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Shetabit\Stampable\Exceptions\StampNotFoundException;

/**
 * @mixin Model
 */
trait HasStamps
{
    /**
     * The prefixes of the dynamic methods, ordered so that a stamp whose own name starts
     * with `un` wins over the negation of the stamp behind it.
     *
     * @var array<string, string>
     */
    private const array BEHAVIORS = [
        'is' => 'isStampedBy',
        'markAs' => 'markAsStamped',
        'isUn' => 'isUnstampedBy',
        'markAsUn' => 'markAsUnstamped',
    ];

    /**
     * The stamps of the model, as `[stampName => fieldName]`.
     *
     * @return array<string, string>
     */
    public function getStamps() : array
    {
        $stamps = [];

        foreach ($this->declaredStamps() as $name => $field) {
            $stamps[is_int($name) ? $field : $name] = $field;
        }

        return $stamps;
    }

    public function hasStamp(string $stampName) : bool
    {
        return isset($this->getStamps()[$stampName]);
    }

    /**
     * @throws StampNotFoundException
     */
    public function getStampField(string $stampName) : string
    {
        $stamps = $this->getStamps();

        return $stamps[$stampName]
            ?? throw StampNotFoundException::forStamp($stampName, array_keys($stamps));
    }

    /**
     * @throws StampNotFoundException
     */
    public function isStampedBy(string $stampName) : bool
    {
        return $this->{$this->getStampField($stampName)} !== null;
    }

    /**
     * @throws StampNotFoundException
     */
    public function isUnstampedBy(string $stampName) : bool
    {
        return ! $this->isStampedBy($stampName);
    }

    /**
     * @throws StampNotFoundException
     */
    public function markAsStamped(string $stampName) : bool
    {
        return $this->forceFill([$this->getStampField($stampName) => $this->freshTimestamp()])->save();
    }

    /**
     * @throws StampNotFoundException
     */
    public function markAsUnstamped(string $stampName) : bool
    {
        return $this->forceFill([$this->getStampField($stampName) => null])->save();
    }

    /**
     * @param  Builder<covariant Model>  $query
     * @return Builder<covariant Model>
     *
     * @throws StampNotFoundException
     */
    public function scopeStamped(Builder $query, string $stampName) : Builder
    {
        return $query->whereNotNull($this->getStampField($stampName));
    }

    /**
     * @param  Builder<covariant Model>  $query
     * @return Builder<covariant Model>
     *
     * @throws StampNotFoundException
     */
    public function scopeUnstamped(Builder $query, string $stampName) : Builder
    {
        return $query->whereNull($this->getStampField($stampName));
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array<int, mixed>  $parameters
     */
    public function __call($method, $parameters) : mixed
    {
        if (($behavior = $this->getStampBehavior($method)) !== null) {
            [$behaviorMethod, $stampName] = $behavior;

            return $this->{$behaviorMethod}($stampName);
        }

        if (($scope = $this->getStampScope($method)) !== null) {
            [$scopeMethod, $stampName] = $scope;

            return $this->forwardCallTo($this->newQuery(), $scopeMethod, [$stampName]);
        }

        return parent::__call($method, $parameters);
    }

    /**
     * The stamps the way the model declares them, either as `[stampName => fieldName]`
     * or as a plain list of field names.
     *
     * @return array<array-key, string>
     */
    private function declaredStamps() : array
    {
        return property_exists($this, 'stamps') ? $this->stamps : [];
    }

    /**
     * The behavior and the stamp a dynamic method such as `markAsPublished` stands for.
     *
     * @return array{string, string}|null
     */
    private function getStampBehavior(string $method) : array|null
    {
        $lowerCaseMethod = strtolower($method);
        $stampNames = array_keys($this->getStamps());

        foreach (self::BEHAVIORS as $prefix => $behavior) {
            foreach ($stampNames as $stampName) {
                if ($lowerCaseMethod === strtolower($prefix.$stampName)) {
                    return [$behavior, $stampName];
                }
            }
        }

        return null;
    }

    /**
     * The scope and the stamp a dynamic method such as `unpublished` stands for.
     *
     * @return array{string, string}|null
     */
    private function getStampScope(string $method) : array|null
    {
        $lowerCaseMethod = strtolower($method);
        $stampNames = array_keys($this->getStamps());

        foreach ($stampNames as $stampName) {
            if ($lowerCaseMethod === strtolower($stampName)) {
                return ['stamped', $stampName];
            }
        }

        foreach ($stampNames as $stampName) {
            if ($lowerCaseMethod === strtolower('un'.$stampName)) {
                return ['unstamped', $stampName];
            }
        }

        return null;
    }
}
