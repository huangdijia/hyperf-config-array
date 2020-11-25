<?php

declare(strict_types=1);
/**
 * This file is part of Smsease.
 *
 * @link     https://github.com/huangdijia/hyperf-config-any
 * @document https://github.com/huangdijia/hyperf-config-any/blob/main/README.md
 * @contact  huangdijia@gmail.com
 * @license  https://github.com/huangdijia/hyperf-config-any/blob/main/LICENSE
 */
namespace Huangdijia\ConfigAny;

interface SourceInterface
{
    public function toArray(): array;
}
