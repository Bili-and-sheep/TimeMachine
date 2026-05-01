<?php

namespace App\Enum;

enum Role: string
{
    case User     = 'ROLE_USER';
    case Reviewer = 'ROLE_REVIEWER';
    case Barista  = 'ROLE_BARISTA';
    case Admin    = 'ROLE_ADMIN';
}
