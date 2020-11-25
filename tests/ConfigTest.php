<?php

namespace Huangdijia\ConfigArray\Tests;

use Hyperf\Testing\Client;
use Psr\Container\ContainerInterface;

class ConfigTest extends TestCase
{
    public function testExample()
    {
        $content = file_get_contents('http://localhost:9501');

        $this->assertSame(date('Y-m-d'), $content);
    }
}