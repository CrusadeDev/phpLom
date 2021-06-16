<?php

declare(strict_types=1);

namespace Crusade\PhpLom\ValueObject;

use Crusade\PhpLom\Nodes\PropertyData;

class GeneratedMethodData
{
    private PropertyData $PropertyData;
    private string $functionName;

    public function __construct(PropertyData $PropertyData, string $functionName)
    {
        $this->PropertyData = $PropertyData;
        $this->functionName = $functionName;
    }

    public function getPropertyData(): PropertyData
    {
        return $this->PropertyData;
    }

    public function getFunctionName(): string
    {
        return $this->functionName;
    }
}