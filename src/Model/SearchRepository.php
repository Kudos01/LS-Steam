<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Model;

interface SearchRepository
{
    public function save(Search $search): void;
}
