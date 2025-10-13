<?php

namespace App\Enum;

enum SiteEnum: string
{
    case SILENUS = 'silenus';
    case INSIDIOME = 'insidiome';

    /**
     * Retourne le code court du site (slns, nsdm)
     */
    public function getCode(): string
    {
        return match($this) {
            self::SILENUS => 'slns',
            self::INSIDIOME => 'nsdm',
        };
    }

    /**
     * Retourne le nom d'affichage du site
     */
    public function getDisplayName(): string
    {
        return match($this) {
            self::SILENUS => 'Silenus',
            self::INSIDIOME => 'Insidiome',
        };
    }

    /**
     * Retourne le domaine du site
     */
    public function getDomain(): string
    {
        return match($this) {
            self::SILENUS => 'silenus.local',
            self::INSIDIOME => 'insidiome.local',
        };
    }

    /**
     * Retourne la couleur primaire du site
     */
    public function getPrimaryColor(): string
    {
        return match($this) {
            self::SILENUS => '#84cc16', // Lime
            self::INSIDIOME => '#a855f7', // Purple
        };
    }

    /**
     * Retourne le préfixe de route du site
     */
    public function getRoutePrefix(): string
    {
        return match($this) {
            self::SILENUS => '/slns',
            self::INSIDIOME => '/nsdm',
        };
    }

    /**
     * Crée une instance depuis un code (slns, nsdm)
     */
    public static function fromCode(string $code): ?self
    {
        return match($code) {
            'slns' => self::SILENUS,
            'nsdm' => self::INSIDIOME,
            default => null,
        };
    }

    /**
     * Crée une instance depuis un nom (silenus, insidiome)
     */
    public static function fromName(string $name): ?self
    {
        return match(strtolower($name)) {
            'silenus' => self::SILENUS,
            'insidiome' => self::INSIDIOME,
            default => null,
        };
    }

    /**
     * Retourne tous les sites disponibles
     */
    public static function all(): array
    {
        return [
            self::SILENUS,
            self::INSIDIOME,
        ];
    }
}
