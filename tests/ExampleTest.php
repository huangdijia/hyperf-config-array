<?php

namespace Huangdijia\ConfigArray\Tests;

use Hyperf\Testing\Client;
use Psr\Container\ContainerInterface;

class ExampleTest extends TestCase
{
    public function testExample()
    {
        $content = file_get_contents('http://127.0.0.1:9501');

        $this->assertSame(date('Y-m-d'), $content);
    }
}