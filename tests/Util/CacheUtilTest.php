<?php

namespace App\Tests\Util;

use Exception;
use App\Util\CacheUtil;
use App\Manager\ErrorManager;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CacheUtilTest
 *
 * Test cases for cache manager util
 *
 * @package App\Tests\Util
 */
class CacheUtilTest extends TestCase
{
    private CacheUtil $cacheUtil;
    private ErrorManager & MockObject $errorManagerMock;
    private CacheItemPoolInterface & MockObject $cacheItemPoolMock;

    protected function setUp(): void
    {
        // mock dependencies
        $this->errorManagerMock = $this->createMock(ErrorManager::class);
        $this->cacheItemPoolMock = $this->createMock(CacheItemPoolInterface::class);

        // create instance of CacheUtil
        $this->cacheUtil = new CacheUtil($this->errorManagerMock, $this->cacheItemPoolMock);
    }

    /**
     * Test check is key catched
     *
     * @return void
     */
    public function testCheckIsKeyCatched(): void
    {
        $key = 'test_key';
        $cacheItemMock = $this->createMock(CacheItemInterface::class);

        // mock cache item pool
        $this->cacheItemPoolMock->expects($this->once())
            ->method('getItem')->with($key)->willReturn($cacheItemMock);

        // mock cache item
        $cacheItemMock->expects($this->once())->method('isHit')->willReturn(true);

        // assert that the method returns true
        $this->assertTrue($this->cacheUtil->isCatched($key));
    }

    /**
     * Test get catched value
     *
     * @return void
     */
    public function testGetCachedValue(): void
    {
        // testing item key
        $key = 'test_key';

        // set cache item mock expectations
        $cacheItemMock = $this->createMock(CacheItemInterface::class);
        $this->cacheItemPoolMock->expects($this->once())
            ->method('getItem')->with($key)->willReturn($cacheItemMock);

        // assert that the method returns the cache item
        $this->assertSame($cacheItemMock, $this->cacheUtil->getValue($key));
    }

    /**
     * Test store value to cache
     *
     * @return void
     */
    public function testStoreValueToCache(): void
    {
        // testing cache item
        $key = 'test_key';
        $value = 'test_value';
        $expiration = 3600;
        $cacheItemMock = $this->createMock(CacheItemInterface::class);

        // set cache item mock expectations
        $this->cacheItemPoolMock->expects($this->once())->method('getItem')->with($key)->willReturn($cacheItemMock);
        $this->cacheItemPoolMock->expects($this->once())->method('save')->with($cacheItemMock);

        // set cache item mock expectations
        $cacheItemMock->expects($this->once())->method('set')->with($value);
        $cacheItemMock->expects($this->once())->method('expiresAfter')->with($expiration);

        // call tested method
        $this->cacheUtil->setValue($key, $value, $expiration);
    }

    /**
     * Test delete value from cache storage
     *
     * @return void
     */
    public function testDeleteValueFromCacheStorage(): void
    {
        // testing cache item key
        $key = 'test_key';

        // set cache item mock expectations
        $this->cacheItemPoolMock->expects($this->once())->method('deleteItem')->with($key);

        // call tested method
        $this->cacheUtil->deleteValue($key);
    }

    /**
     * Test set value to cache storage with exception throw
     *
     * @return void
     */
    public function testSetValueToCacheStorageWithExceptionThrow(): void
    {
        // testing cache item data
        $key = 'test_key';
        $value = 'test_value';
        $expiration = 3600;

        // set cache item mock expectations
        $this->cacheItemPoolMock->expects($this->once())
            ->method('getItem')->with($key)->willThrowException(new Exception('Test exception'));

        // set error manager mock expectations
        $this->errorManagerMock->expects($this->once())->method('handleError')->with(
            msg: 'error to store cache value: Test exception',
            code: Response::HTTP_INTERNAL_SERVER_ERROR
        );

        // call tested method
        $this->cacheUtil->setValue($key, $value, $expiration);
    }

    /**
     * Test delete value from cache storage with exception throw
     *
     * @return void
     */
    public function testDeleteValueFromCacheStorageWithExceptionThrow(): void
    {
        // testing cache item key
        $key = 'test_key';

        // set cache item mock expectations
        $this->cacheItemPoolMock->expects($this->once())
            ->method('deleteItem')->with($key)->willThrowException(new Exception('Test exception'));

        // set error manager mock expectations
        $this->errorManagerMock->expects($this->once())->method('handleError')->with(
            msg: 'error to delete cache value: Test exception',
            code: Response::HTTP_INTERNAL_SERVER_ERROR
        );

        // call tested method
        $this->cacheUtil->deleteValue($key);
    }
}
