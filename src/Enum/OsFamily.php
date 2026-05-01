<?php

namespace App\Enum;

enum OsFamily: string
{
    case iOS = 'iOS';
    case MacOS = 'macOS';
    case WatchOS = 'watchOS';
    case TvOS = 'tvOS';
    case IPadOS = 'iPadOS';
}
