<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Request;

interface EntityFactory
{
    public function create(string $json);
}