<?php
declare(strict_types=1);

namespace App\Enum;

enum EventStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Cancelled = 'cancelled';
}
