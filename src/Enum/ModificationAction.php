<?php

namespace App\Enum;

enum ModificationAction: string
{
    case Created       = 'created';
    case StatusChanged = 'status_changed';
    case Edited        = 'edited';

    public function label(): string
    {
        return match($this) {
            self::Created       => 'Created',
            self::StatusChanged => 'Status changed',
            self::Edited        => 'Edited',
        };
    }
}
