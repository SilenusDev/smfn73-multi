<?php

namespace App\Enum;

enum BlogStatusEnum: string
{
    case DRAFT = 'brouillon';
    case PUBLISHED = 'publie';
    case PRIVATE = 'prive';
    case PREMIUM = 'premium';

    /**
     * Retourne le label d'affichage
     */
    public function getLabel(): string
    {
        return match($this) {
            self::DRAFT => 'Brouillon',
            self::PUBLISHED => 'Publié',
            self::PRIVATE => 'Privé',
            self::PREMIUM => 'Premium',
        };
    }

    /**
     * Retourne la couleur pour l'affichage (Tailwind)
     */
    public function getColor(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::PUBLISHED => 'green',
            self::PRIVATE => 'orange',
            self::PREMIUM => 'purple',
        };
    }

    /**
     * Retourne l'icône Lucide
     */
    public function getIcon(): string
    {
        return match($this) {
            self::DRAFT => 'file-edit',
            self::PUBLISHED => 'check-circle',
            self::PRIVATE => 'lock',
            self::PREMIUM => 'crown',
        };
    }

    /**
     * Vérifie si le statut est visible publiquement
     */
    public function isPublic(): bool
    {
        return $this === self::PUBLISHED;
    }

    /**
     * Vérifie si le statut nécessite une authentification
     */
    public function requiresAuth(): bool
    {
        return in_array($this, [self::PRIVATE, self::PREMIUM]);
    }

    /**
     * Vérifie si le statut nécessite un compte premium
     */
    public function requiresPremium(): bool
    {
        return $this === self::PREMIUM;
    }

    /**
     * Retourne tous les statuts disponibles
     */
    public static function all(): array
    {
        return [
            self::DRAFT,
            self::PUBLISHED,
            self::PRIVATE,
            self::PREMIUM,
        ];
    }

    /**
     * Retourne les statuts visibles (non brouillon)
     */
    public static function visible(): array
    {
        return [
            self::PUBLISHED,
            self::PRIVATE,
            self::PREMIUM,
        ];
    }
}
