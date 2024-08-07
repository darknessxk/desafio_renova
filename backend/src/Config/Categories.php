<?php

namespace App\Config;

enum Categories: string
{
    case School = 'school';
    case University = 'university';
    case Charity = 'charity';
    case Health = 'health';
    case Technology = 'technology';
    case Environment = 'environment';
    case Animals = 'animals';
    case Art = 'art';
    case Music = 'music';
    case Film = 'film';
    case Food = 'food';
}