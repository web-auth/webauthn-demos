<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CredentialRecord;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Webauthn\Bundle\Repository\CanSaveCredentialRecord;
use Webauthn\Bundle\Repository\CredentialRecordRepositoryInterface;
use Webauthn\CredentialRecord as BaseCredentialRecord;
use Webauthn\PublicKeyCredentialUserEntity;

/**
 * @extends ServiceEntityRepository<CredentialRecord>
 */
final class CredentialRecordRepository extends ServiceEntityRepository implements CredentialRecordRepositoryInterface, CanSaveCredentialRecord
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CredentialRecord::class);
    }

    public function findOneByCredentialId(string $publicKeyCredentialId): ?BaseCredentialRecord
    {
        return $this->findOneBy([
            'publicKeyCredentialId' => $publicKeyCredentialId,
        ]);
    }

    /**
     * @return CredentialRecord[]
     */
    public function findAllForUserEntity(PublicKeyCredentialUserEntity $publicKeyCredentialUserEntity): array
    {
        return $this->findBy([
            'userHandle' => $publicKeyCredentialUserEntity->id,
        ]);
    }

    public function saveCredentialRecord(BaseCredentialRecord $credentialRecord): void
    {
        if (! $credentialRecord instanceof CredentialRecord) {
            $credentialRecord = CredentialRecord::fromCredentialRecord($credentialRecord);
        }

        $this->getEntityManager()
            ->persist($credentialRecord);
        $this->getEntityManager()
            ->flush();
    }

    /**
     * @return CredentialRecord[]
     */
    public function allForUser(User $user): array
    {
        return $this->findBy([
            'userHandle' => $user->getId(),
        ]);
    }

    public function removeAllOfUser(User $user): void
    {
        foreach ($this->allForUser($user) as $credentialRecord) {
            $this->getEntityManager()
                ->remove($credentialRecord);
        }
        $this->getEntityManager()
            ->flush();
    }
}
