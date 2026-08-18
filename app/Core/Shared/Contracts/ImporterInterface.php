<?php

namespace App\Core\Shared\Contracts;

interface ImporterInterface
{
    /**
     * Import data dari file.
     */
    public function import(
        string $path,
        array $options = []
    ): array;
}