# Hyperf config-array

[![Latest Test](https://github.com/huangdijia/hyperf-config-array/workflows/tests/badge.svg)](https://github.com/huangdijia/hyperf-config-array/actions)
[![Latest Stable Version](https://poser.pugx.org/huangdijia/hyperf-config-array/version.png)](https://packagist.org/packages/huangdijia/hyperf-config-array)
[![Total Downloads](https://poser.pugx.org/huangdijia/hyperf-config-array/d/total.png)](https://packagist.org/packages/huangdijia/hyperf-config-array)
[![GitHub license](https://img.shields.io/github/license/huangdijia/hyperf-config-array)](https://github.com/huangdijia/hyperf-config-array)

## Installation

~~~base
composer require huangdijia/hyperf-config-array
~~~

## Publish

~~~bash
php bin/hyperf.php vendor:publish huangdijia/hyperf-config-array
~~~

## Define source

~~~php
namespace App\Source;

use Huangdijia\ConfigArray\SourceInterface;
use Hyperf\DB\DB;

class DBSource implements SourceInterface
{
    public function toArray(): array
    {
        return DB::query('SELECT * FROM `config`;');
    }
}
~~~

## Set config

~~~php
// config/autoload/config_array.php
return [
    // ...
    'source' => App\Source\DBSource::class,
    // ...
    'mapping' => 'setting', // using as config('setting')
    // or
    'mapping' => [
        'setting_key' => 'setting.key', // using as config('setting.key')
    ],
];
~~~
