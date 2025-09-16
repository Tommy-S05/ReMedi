<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Medication;
use App\Models\Prescription;
use InvalidArgumentException;

final class ShareableTypeRegistry
{
    /**
     * @var array<string, class-string>
     */
    private static array $types = [
        'medication' => Medication::class,
        'prescription' => Prescription::class,
        // Add more shareable types here in the future.
    ];

    /**
     * Get the model class for a given shareable type string.
     *
     * @param string $type  The shareable type string.el class string.
     *
     * @throws InvalidArgumentException
     */
    public static function getModelClass(string $type): string
    {
        // @return class-string The mod
        if (!isset(self::$types[$type])) {
            throw new InvalidArgumentException("Invalid shareable type: {$type}");
        }

        return self::$types[$type];
    }

    /**
     * Get an array of all valid shareable type strings.
     *
     * @return string[] The array of valid shareable type strings.
     */
    public static function getValidTypes(): array
    {
        return array_keys(self::$types);
    }

    /**
     * Check if a given type string is a valid shareable type.
     *
     * @param string $type The shareable type string.
     * @return bool True if the type is valid, false otherwise.
     */
    public static function isValidType(string $type): bool
    {
        return isset(self::$types[$type]);
    }

    public static function getShareableType(string $modelClass): string
    {
        $type = array_search($modelClass, self::$types, true);

        if ($type === false) {
            throw new InvalidArgumentException("Invalid model class: {$modelClass}");
        }

        return $type;
    }
}
