<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class CurrentUserProvider
{
    public function __construct(
        private UserRepository $userRepository,
        private readonly int $currentUserId
    ) {
    }

    public function getCurrentUserId(): int
    {
        return $this->currentUserId;
    }

    public function getCurrentUser(): ?User
    {
        return $this->userRepository->find($this->currentUserId);
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