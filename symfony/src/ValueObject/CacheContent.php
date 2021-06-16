<?php

declare(strict_types=1);

namespace Crusade\PhpLom\ValueObject;

class CacheContent
{
    /**
     * @param array<string, string> $content
     */
    private array $content;

    /**
     * @param array<string, string> $content
     */
    public function __construct(array $content)
    {
        $this->content = $content;
    }

    public function add(string $key, array $value): self
    {
        $clone = $this->content;

        $clone[$key] = $value;

        return new self ($clone);
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return $this->content;
    }
}