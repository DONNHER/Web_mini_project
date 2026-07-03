<?php

namespace App\Traits;

trait Shardable
{
    /**
     * Get the connection name for the shard based on the model ID.
     * Uses modulo-based routing (id % 4) for 4 shards.
     */
    public function getShardConnection(): string
    {
        if (!$this->id) {
            return config('database.default');
        }

        $shardId = $this->id % 4; // 4 shards
        return "mysql_shard_{$shardId}";
    }

    /**
     * Override the connection for the model instance.
     */
    public function onShard()
    {
        return $this->setConnection($this->getShardConnection());
    }
}
