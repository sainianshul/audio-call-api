<?php

namespace App\Enums;

enum CallStatus: string
{
    case Initiated = 'initiated';
    case Accepted  = 'accepted';
    case Rejected  = 'rejected';
    case Ended     = 'ended';
}
