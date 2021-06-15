<?php

declare(strict_types=1);

namespace App\ValueObject;

class FileName
{
    private string $fileName;

    private function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public static function overrideCache(): self
    {
        return new self('overrideCache.json');
    }

    public static function checkedFilesCache(): self
    {
        return new self('checked.json');
    }

    public static function fromString(string $fileName): self
    {
        return new self($fileName);
    }

    public function __toString(): string
    {
        return $this->fileName;
    }
}