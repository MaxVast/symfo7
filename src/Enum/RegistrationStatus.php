<?php

declare(strict_types=1);

namespace App\Enum;
enum RegistrationStatus: string
{
    case Confirmed = 'confirmed';
    case Waitlist = 'waitlist';
    case Cancelled = 'cancelled';
}
