<?php

/**
 * @noinspection PhpMultipleClassDeclarationsInspection
 */

declare(strict_types=1);

namespace App\Parser;

use App\ValueObject\JsonContent;

class JsonParser
{
    public function decode(JsonContent $json): array
    {
        try {
            return json_decode((string)$json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \LogicException($e->getMessage());
        }
    }

    public function encode(array $content): JsonContent
    {
        try {
            return new JsonContent(json_encode($content, JSON_THROW_ON_ERROR));
        } catch (\JsonException $e) {
            throw new \LogicException($e->getMessage());
        }
    }
}