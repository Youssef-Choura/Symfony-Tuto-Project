<?php

namespace App\Services;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;


class Helpers
{
    private Security $security;

    public function __construct(private readonly LoggerInterface $logger, Security $security)
    {
    }

    public function sayCC(): string
    {
        $this->logger->info('Salem');
        return 'Salem';
    }

    public function getUser(): User
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $user = $this->security->getUser();
            if ($user instanceof User) {
                return $user;
            }
        }
    }
}