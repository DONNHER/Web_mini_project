<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait OptimisticLocking
{
    public static function bootOptimisticLocking()
    {
        static::updating(function ($model) {
            $model->version++;
        });

        static::addGlobalScope('lockVersion', function (Builder $builder) {
            // In a real implementation, we would check the version on save
            // Eloquent doesn't have native optimistic locking like JPA,
            // so we simulate it by adding the version to the where clause on update
        });
    }

    /**
     * Override performUpdate to include version check
     */
    protected function performUpdate(Builder $query)
    {
        $originalVersion = $this->getOriginal('version');

        $query->where('version', $originalVersion);

        $result = parent::performUpdate($query);

        if ($result === 0) {
            throw new \Exception("Record was modified by another user. Please refresh and try again.");
        }

        return $result;
    }
}
