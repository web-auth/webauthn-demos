<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CredentialRecordRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Webauthn\CredentialRecord as BaseCredentialRecord;
use Webauthn\TrustPath\TrustPath;

#[ORM\Table(name: 'pk_credential_sources')]
#[ORM\Entity(repositoryClass: CredentialRecordRepository::class)]
class CredentialRecord extends BaseCredentialRecord
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public readonly DateTimeImmutable $createdAt;

    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 255)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private string $id;

    /**
     * @param string[] $transports
     * @param array<string, mixed>|null $otherUI
     */
    public function __construct(
        string $publicKeyCredentialId,
        string $type,
        array $transports,
        string $attestationType,
        TrustPath $trustPath,
        Uuid $aaguid,
        string $credentialPublicKey,
        string $userHandle,
        int $counter,
        ?array $otherUI = null,
        ?bool $backupEligible = null,
        ?bool $backupStatus = null,
        ?bool $uvInitialized = null,
    ) {
        $this->id = Uuid::v4()->toRfc4122();
        $this->createdAt = new DateTimeImmutable();
        parent::__construct(
            $publicKeyCredentialId,
            $type,
            $transports,
            $attestationType,
            $trustPath,
            $aaguid,
            $credentialPublicKey,
            $userHandle,
            $counter,
            $otherUI,
            $backupEligible,
            $backupStatus,
            $uvInitialized
        );
    }

    public static function fromCredentialRecord(BaseCredentialRecord $credentialRecord): self
    {
        return new self(
            $credentialRecord->publicKeyCredentialId,
            $credentialRecord->type,
            $credentialRecord->transports,
            $credentialRecord->attestationType,
            $credentialRecord->trustPath,
            $credentialRecord->aaguid,
            $credentialRecord->credentialPublicKey,
            $credentialRecord->userHandle,
            $credentialRecord->counter,
            $credentialRecord->otherUI,
            $credentialRecord->backupEligible,
            $credentialRecord->backupStatus,
            $credentialRecord->uvInitialized
        );
    }

    public function getId(): string
    {
        return $this->id;
    }
}
