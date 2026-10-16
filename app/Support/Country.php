<?php

namespace App\Support;

class Country
{
    private const LABELS = [
        'SN' => 'Sénégal',
        'KM' => 'Comores',
        'FR' => 'France',
        'CI' => "Côte d'Ivoire",
        'CN' => 'Chine',
    ];

    private const CITIES = [
        'SN' => 'Dakar',
        'KM' => 'Moroni',
        'FR' => 'Paris',
    ];

    public static function normalize(string $country): string
    {
        $country = preg_replace('/\s+/u', ' ', trim($country)) ?? trim($country);
        $alias = mb_strtolower($country);

        $aliases = [
            'sn' => 'SN',
            'sénégal' => 'SN',
            'senegal' => 'SN',
            'km' => 'KM',
            'comores' => 'KM',
            'comoros' => 'KM',
            'fr' => 'FR',
            'france' => 'FR',
            'ci' => 'CI',
            "côte d'ivoire" => 'CI',
            "cote d'ivoire" => 'CI',
            'cn' => 'CN',
            'chine' => 'CN',
            'china' => 'CN',
        ];

        return $aliases[$alias] ?? mb_convert_case($country, MB_CASE_TITLE, 'UTF-8');
    }

    public static function label(string $country): string
    {
        $normalized = self::normalize($country);

        return self::LABELS[$normalized] ?? $normalized;
    }

    public static function city(string $country): string
    {
        $normalized = self::normalize($country);

        return self::CITIES[$normalized] ?? self::label($normalized);
    }

    /**
     * @return list<string>
     */
    public static function suggestions(): array
    {
        return [
            'Sénégal',
            'Comores',
            'France',
            "Côte d'Ivoire",
            'Chine',
            'Maroc',
            'Mali',
            'Guinée',
            'Nigeria',
            'Ghana',
            'Afrique du Sud',
            'États-Unis',
            'Canada',
            'Brésil',
            'Royaume-Uni',
            'Allemagne',
            'Belgique',
            'Italie',
            'Espagne',
            'Émirats arabes unis',
            'Inde',
            'Japon',
            'Australie',
        ];
    }

    /**
     * @return list<array{0: string, 1: string}>
     */
    public static function popularRoutes(): array
    {
        return [
            ['SN', 'KM'],
            ['KM', 'SN'],
            ['SN', 'FR'],
            ['FR', 'SN'],
            ['SN', 'CI'],
            ['CI', 'SN'],
            ['CN', 'SN'],
            ['SN', 'CN'],
            ['Maroc', 'SN'],
            ['SN', 'Maroc'],
        ];
    }
}
