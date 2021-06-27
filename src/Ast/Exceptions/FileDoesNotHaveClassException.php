<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Ast\Exceptions;

use Crusade\PhpLom\Ast\ValueObject\ParsedFile;

class FileDoesNotHaveClassException extends \Exception
{
    public static function create(ParsedFile $ast): self
    {
        return new self('Tried attaching methods to non existing class filePath: '.$ast->getFilePath());
    }
}