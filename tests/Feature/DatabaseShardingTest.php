<?php

namespace Tests\Feature;

use App\Models\Book;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DatabaseShardingTest extends TestCase
{
    #[Test]
    public function it_calculates_correct_shard_connection()
    {
        $book = new Book();

        // Shard 0: ID 4, 8, 12...
        $book->id = 4;
        $this->assertEquals('mysql_shard_0', $book->getShardConnection());

        // Shard 1: ID 1, 5, 9...
        $book->id = 5;
        $this->assertEquals('mysql_shard_1', $book->getShardConnection());

        // Shard 2: ID 2, 6, 10...
        $book->id = 10;
        $this->assertEquals('mysql_shard_2', $book->getShardConnection());

        // Shard 3: ID 3, 7, 11...
        $book->id = 11;
        $this->assertEquals('mysql_shard_3', $book->getShardConnection());
    }

    #[Test]
    public function it_can_switch_to_shard_connection()
    {
        $book = new Book();
        $book->id = 1;

        $shardedBook = $book->onShard();

        $this->assertEquals('mysql_shard_1', $shardedBook->getConnectionName());
    }
}
