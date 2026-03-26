<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

class CurrentUserProvider
{
    public function __construct(
        private Security $security
    ) {
    }

    public function getCurrentUser(): ?User
    {
        $user = $this->security->getUser();

        return $user instanceof User ? $user : null;
    }

    public function getCurrentUserId(): ?int
    {
        return $this->getCurrentUser()?->getId();
    }

    public function hasCurrentUser(): bool
    {
        return $this->getCurrentUser() !== null;
    }

    public function isAdmin(): bool
    {
        $user = $this->getCurrentUser();

        return $user !== null && $user->isAdmin() === true;
    }

    public function isSuperAdmin(): bool
    {
        $user = $this->getCurrentUser();

        return $user !== null && $user->isSuperAdmin() === true;
    }
}