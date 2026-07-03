<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class RedisIntegrationTest extends TestCase
{
    /** @test */
    public function it_uses_redis_for_caching_with_tag_support()
    {
        // 1. Force Redis Driver for the test (overriding phpunit.xml)
        config(['cache.default' => 'redis']);

        // 2. Verify Cache Driver is Redis
        $this->assertEquals('redis', config('cache.default'));

        // 3. Test Tagging (This fails if driver is 'file' or 'database')
        Cache::tags(['performance', 'books'])->put('test_key', 'tag_value', 60);

        $this->assertEquals('tag_value', Cache::tags(['performance', 'books'])->get('test_key'));

        // 4. Verify it's actually in Redis Database 1
        $redis = Redis::connection('cache');
        $this->assertNotEmpty($redis->keys('*'), 'Redis Cache DB should not be empty');
    }

    /** @test */
    public function it_uses_redis_for_sessions()
    {
        // 1. Force Redis Driver for the test
        config(['session.driver' => 'redis']);
        config(['session.connection' => 'session']);

        // 2. Verify Session Driver
        $this->assertEquals('redis', config('session.driver'));
        $this->assertEquals('session', config('session.connection'));

        // 3. Put something in session and SAVE it
        session(['redis_test' => 'session_value']);
        Session::save();

        // 4. Verify it's in Redis Database 2
        $redis = Redis::connection('session');
        // We use a wildcard but we don't assume the prefix
        $keys = $redis->keys('*');
        $this->assertNotEmpty($keys, 'Redis Session DB should not be empty after Session::save()');
    }

    /** @test */
    public function it_allocates_separate_databases_for_cache_and_sessions()
    {
        // 1. Force Drivers
        config(['cache.default' => 'redis']);
        config(['session.driver' => 'redis']);
        config(['session.connection' => 'session']);

        // 2. Clear both for clean test
        Redis::connection('cache')->flushdb();
        Redis::connection('session')->flushdb();

        // 3. Put data in Cache (DB 1)
        Cache::put('cache_only_key', 'data', 60);

        // 4. Put data in Session (DB 2)
        session(['session_only_key' => 'data']);
        Session::save();

        // 5. Verify isolation by checking keys count in each DB
        // DB 1 should have the cache key
        $this->assertNotEmpty(Redis::connection('cache')->keys('*'), 'Cache DB should have keys');

        // DB 2 should have the session key
        $this->assertNotEmpty(Redis::connection('session')->keys('*'), 'Session DB should have keys');

        // Cross-check: The specific cache key should NOT be in the session DB
        $cacheKeys = Redis::connection('cache')->keys('*');
        $sessionKeys = Redis::connection('session')->keys('*');

        foreach ($cacheKeys as $key) {
            $this->assertNotContains($key, $sessionKeys, 'Cache key should not exist in Session DB');
        }
    }
}
