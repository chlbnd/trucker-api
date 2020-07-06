<?php

namespace App\Entity;

interface EntityInterface extends \JsonSerializable
{
    public function getId();
}