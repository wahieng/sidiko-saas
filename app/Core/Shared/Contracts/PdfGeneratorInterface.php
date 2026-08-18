<?php

namespace App\Core\Shared\Contracts;

interface PdfGeneratorInterface
{
    public function generate(
        string $filename,
        mixed $view,
        array $data = [],
        array $options = []
    ): string;
}