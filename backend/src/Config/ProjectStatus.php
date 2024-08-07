<?php

namespace App\Config;

enum ProjectStatus: string
{
    case Open = 'open';
    case Completed = 'completed';
}