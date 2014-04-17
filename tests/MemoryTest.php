<?php
/**
 * @copyright ©2014 Quicken Loans Inc. All rights reserved. Trade Secret,
 *    Confidential and Proprietary. Any dissemination outside of Quicken Loans
 *    is strictly prohibited.
 */

namespace MCP\Cache;

use PHPUnit_Framework_TestCase;

class MemoryTest extends PHPUnit_Framework_TestCase
{
    public function testSettingAKeyAndGetSameKeyResultsInOriginalValue()
    {
        $inputValue = ['myval' => 1];
        $expected = $inputValue;

        $cache = new Memory;
        $cache->set('mykey', $inputValue);

        $actual = $cache->get('mykey');
        $this->assertSame($expected, $actual);
    }

    public function testGettingKeyThatWasNotSetReturnsNullAndNoError()
    {
        $inputKey = 'key-with-no-value';

        $cache = new Memory;

        $actual = $cache->get($inputKey);
        $this->assertNull($actual);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Resources cannot be cached
     */
    public function testCachingResourceBlowsUp()
    {
        $cache = new Memory;
        $actual = $cache->set('key', fopen('php://stdout', 'w'));
    }
}
