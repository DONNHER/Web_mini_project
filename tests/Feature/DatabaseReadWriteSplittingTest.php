<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DatabaseReadWriteSplittingTest extends TestCase
{
    #[Test]
    public function it_has_correct_read_write_splitting_configuration()
    {
        $config = Config::get('database.connections.mysql');

        // 1. Verify Read Configuration exists and has multiple hosts
        $this->assertArrayHasKey('read', $config, 'MySQL connection should have a "read" key.');
        $this->assertIsArray($config['read']['host'], 'Read host should be an array for multiple replicas.');
        $this->assertCount(2, $config['read']['host'], 'Read host should contain 2 entries.');

        // 2. Verify Write Configuration exists
        $this->assertArrayHasKey('write', $config, 'MySQL connection should have a "write" key.');
        $this->assertIsArray($config['write']['host'], 'Write host should be an array.');
        $this->assertCount(1, $config['write']['host'], 'Write host should contain 1 entry.');

        // 3. Verify Sticky is enabled
        $this->assertTrue($config['sticky'], 'Sticky should be enabled for read-after-write consistency.');
    }

    #[Test]
    public function it_uses_sticky_connections()
    {
        $connection = DB::connection('mysql');

        // The 'sticky' option is handled by the DatabaseManager when creating the connection.
        // We can verify if the config assigned to the connection has it.
        $this->assertTrue($connection->getConfig('sticky'));
    }
}
