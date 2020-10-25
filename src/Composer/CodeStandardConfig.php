<?php declare(strict_types=1);

namespace Qlimix\CodeStandard\Composer;

final class CodeStandardConfig
{
    private string $name;
    private string $resourcePath;
    private string $destinationPath;

    public function __construct(string $name, string $resourcePath, string $destinationPath)
    {
        $this->name = $name;
        $this->resourcePath = $resourcePath;
        $this->destinationPath = $destinationPath;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getResourcePath(): string
    {
        return $this->resourcePath;
    }

    public function getDestinationPath(): string
    {
        return $this->destinationPath;
    }
}
