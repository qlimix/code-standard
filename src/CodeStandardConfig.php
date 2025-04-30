<?php declare(strict_types=1);

namespace Qlimix\CodeStandard;

final class CodeStandardConfig
{
    public function __construct(
        public readonly string $name,
        public readonly string $resourcePath,
        public readonly string $destinationPath
    ){}
}
