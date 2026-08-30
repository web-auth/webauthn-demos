<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CredentialRecord;
use App\Entity\User;
use App\Repository\CredentialRecordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Webauthn\Bundle\Repository\PublicKeyCredentialUserEntityRepositoryInterface;

final class AuthenticatorsController extends AbstractController
{
    public function __construct(
        private readonly PublicKeyCredentialUserEntityRepositoryInterface $keyCredentialUserEntityRepository,
        private readonly CredentialRecordRepository $credentialRecordRepository,
    ) {
    }

    #[Route('/profile/authenticators', name: 'authenticators', methods: [Request::METHOD_GET])]
    public function __invoke(): Response
    {
        $user = $this->getUser();
        if (! $user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        $userEntity = $this->keyCredentialUserEntityRepository->findOneByUserHandle($user->getId());
        if ($userEntity === null) {
            throw new AccessDeniedHttpException();
        }

        $credentials = array_map(
            static fn (CredentialRecord $credentialRecord): array => [
                'publicKeyCredentialId' => $credentialRecord->publicKeyCredentialId,
                'aaguid' => $credentialRecord->aaguid->toRfc4122(),
                'attestationType' => $credentialRecord->attestationType,
                'credentialPublicKey' => base64_encode($credentialRecord->credentialPublicKey),
                'counter' => $credentialRecord->counter,
                'createdAt' => $credentialRecord->createdAt,
            ],
            $this->credentialRecordRepository->findAllForUserEntity($userEntity)
        );

        return $this->render('authenticators/index.html.twig', [
            'credentials' => $credentials,
        ]);
    }
}
