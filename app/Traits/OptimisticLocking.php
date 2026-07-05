<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait OptimisticLocking
{
    public static function bootOptimisticLocking()
    {
        // Removed updating hook to test performUpdate logic
    }

    /**
     * Override performUpdate to include version check
     */
    protected function performUpdate(Builder $query)
    {
        $originalVersion = $this->getOriginal('version');

        $currentVersionInDb = \Illuminate\Support\Facades\DB::table($this->getTable())
            ->where($this->getKeyName(), $this->getKey())
            ->value('version');

        if ($currentVersionInDb !== $originalVersion) {
             throw new \Exception("Record was modified by another user. Please refresh and try again.");
        }

        $this->version = $originalVersion + 1;

        return parent::performUpdate($query);
    }
}
