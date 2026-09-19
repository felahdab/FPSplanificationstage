<?php

namespace Modules\FPSplanificationstage\Traits;

use App\Traits\HasTablePrefix as BasePrefixTrait;

trait HasTablePrefix
{
    use BasePrefixTrait;

    protected $prefix = 'fpsplanificationstage_';
}