<?php

declare(strict_types=1);

namespace App\Metadata;

use InvalidArgumentException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Serializer\SerializerInterface;
use Webauthn\MetadataService\Service\MetadataService;
use Webauthn\MetadataService\Statement\MetadataStatement;
use function array_key_exists;
use function sprintf;

final class SingleFileService implements MetadataService
{
    /**
     * @var array<string, MetadataStatement>
     */
    private array $statements = [];

    public function __construct(
        private readonly string $rootPath,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function list(): iterable
    {
        $this->loadMDS();

        yield from array_keys($this->statements);
    }

    public function has(string $aaguid): bool
    {
        $this->loadMDS();

        return array_key_exists($aaguid, $this->statements);
    }

    public function get(string $aaguid): MetadataStatement
    {
        $this->loadMDS();
        array_key_exists($aaguid, $this->statements) || throw new InvalidArgumentException(sprintf(
            'The MDS with the AAGUID "%s" does not exist.',
            $aaguid
        ));

        return $this->statements[$aaguid];
    }

    private function loadMDS(): void
    {
        if ($this->statements !== []) {
            return;
        }

        foreach ($this->getFilenames() as $filename) {
            $data = file_get_contents($filename);
            if ($data === false) {
                continue;
            }
            $mds = $this->serializer->deserialize($data, MetadataStatement::class, 'json');
            if ($mds->aaguid === null) {
                continue;
            }
            $this->statements[$mds->aaguid] = $mds;
        }
    }

    /**
     * @return iterable<string>
     */
    private function getFilenames(): iterable
    {
        $finder = new Finder();
        $finder->files()
            ->in($this->rootPath . '/src/Resources/metadataStatements')
        ;

        foreach ($finder->files() as $file) {
            yield $file->getRealPath();
        }
    }
}
