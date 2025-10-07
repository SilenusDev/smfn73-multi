<?php

namespace App\Security;

use App\Entity\User;
use App\Service\DatabaseManager;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * UserProvider contextuel qui charge les users depuis la bonne base de données
 */
class UserProvider implements UserProviderInterface
{
    public function __construct(
        private DatabaseManager $dbManager
    ) {}

    /**
     * Charge un user par son identifiant (email)
     * Utilise automatiquement la base du site courant
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->dbManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $identifier]);

        if (!$user) {
            throw new UserNotFoundException(sprintf('User "%s" not found.', $identifier));
        }

        return $user;
    }

    /**
     * Rafraîchit un user depuis la base de données
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new \InvalidArgumentException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    /**
     * Vérifie si ce provider supporte la classe User
     */
    public function supportsClass(string $class): bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }

    /**
     * Alias pour compatibilité Symfony < 6
     * @deprecated
     */
    public function loadUserByUsername(string $username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }
}
