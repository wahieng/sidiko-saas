<?php

namespace App\Core\Shared\Contracts;

interface ExporterInterface
{
    public function export(
        string $filename,
        array $data,
        array $options = []
    ): string;
}