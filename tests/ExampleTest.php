<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-config-array.
 *
 * @link     https://github.com/huangdijia/hyperf-config-array
 * @document https://github.com/huangdijia/hyperf-config-array/blob/main/README.md
 * @contact  huangdijia@gmail.com
 * @license  https://github.com/huangdijia/hyperf-config-array/blob/main/LICENSE
 */
namespace Huangdijia\ConfigArray\Tests;

/**
 * @internal
 * @coversNothing
 */
class ExampleTest extends TestCase
{
    public function testExample()
    {
        $url = 'http://127.0.0.1:9501';
        // $content = file_get_contents($curl);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $content = curl_exec($curl);

        $this->assertSame(date('Y-m-d'), $content);
    }
}
