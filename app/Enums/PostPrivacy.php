<?php

namespace App\Enums;

use ArchTech\Enums\Values;

enum PostPrivacy: string
{
    use Values;
    case PUBLIC = 'public';
    case PRIVATE = 'private';
    case FOLLOWERS = 'followers';
}
