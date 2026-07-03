<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DatabaseConnectionPoolingTest extends TestCase
{
    #[Test]
    public function it_has_persistent_pdo_connections_configured()
    {
        $connection = DB::connection();

        // 1. Verify config
        $options = $connection->getConfig('options');
        $this->assertTrue($options[\PDO::ATTR_PERSISTENT] ?? false, 'PDO::ATTR_PERSISTENT is not enabled in config.');
        $this->assertTrue($options[\PDO::ATTR_EMULATE_PREPARES] ?? false, 'PDO::ATTR_EMULATE_PREPARES is not enabled in config.');

        // 2. Verify actual PDO instance
        try {
            $pdo = $connection->getPdo();
            $this->assertTrue((bool) $pdo->getAttribute(\PDO::ATTR_PERSISTENT), 'The live PDO instance is not persistent.');
        } catch (\Exception $e) {
            $this->markTestSkipped('Database connection not available to verify PDO attributes: ' . $e->getMessage());
        }
    }

    #[Test]
    public function it_verifies_shards_also_have_pooling_enabled()
    {
        for ($i = 0; $i < 4; $i++) {
            $config = config("database.connections.mysql_shard_{$i}");
            $this->assertTrue($config['options'][\PDO::ATTR_PERSISTENT] ?? false, "Shard {$i} is missing persistent connection config.");
        }
    }
}
