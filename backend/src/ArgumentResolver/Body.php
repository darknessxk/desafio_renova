<?php

namespace App\ArgumentResolver;


use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
final class Body {
    public function __construct(
        public bool $validate = true
    ) { }
}