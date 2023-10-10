<?php

namespace App\Enums;

enum MutationType: string
{
    case sales = 'sales';
    case filling = 'filling';
    case correction = 'correction';
}
