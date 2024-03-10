<?php

namespace App\Enums;

use Generator;
use Illuminate\Support\Arr;

/**
 * Class BaseEnum
 *
 * @package App\Enums
 */
abstract class BaseEnum
{
    /**
     * Defines labels for each class constant
     *
     * @return array
     */
    abstract public static function getLabels(): array;

    /**
     * Get filtered labels
     *
     * @param array $keys
     * @return array
     */
    public static function filterLabels(array $keys = []): array
    {
        return iterator_to_array(static::filterLabelsIterator($keys));
    }

    /**
     * Filtered labels iterator
     *
     * @param array $keys
     * @return Generator
     */
    protected static function filterLabelsIterator(array $keys = []): Generator
    {
        if (count($keys) === 0)
            return null;

        foreach ($keys as $key) {
            yield $key => static::getLabels()[$key];
        }
    }

    /**
     * Get all constants values
     *
     * @return array
     */
    public static function getKeys(): array
    {
        return array_keys(static::getKeysAsValues());
    }

    /**
     * Get all labels values
     *
     * @return array
     */
    public static function getValues(): array
    {
        return array_values(static::getLabels());
    }

    /**
     * Get labels array as associate array
     *
     * @return array
     */
    public static function getAssociate(): array
    {
        $array = [];
        foreach (static::getLabels() as $key => $value) {
            $array[] = ['id' => $key, 'title' => $value];
        }

        return $array;
    }

    /**
     * Get label value by key
     *
     * @param int|string $key
     * @return int|string|null
     */
    public static function getValue($key)
    {
        return Arr::get(static::getLabels(), $key);
    }

    /**
     * Get all class constants as associate array
     *
     * @param array $keys
     * @return array
     */
    public static function getKeysAsValues(array $keys = []): array
    {
        if (!count($keys)) {
            $ref = new \ReflectionClass(static::class);
            $keys = $ref->getConstants();
        }

        $iterator = function () use ($keys) {
            foreach ($keys as $key) {
                if (!is_array($key))
                    yield $key => $key;
            }
        };

        return iterator_to_array($iterator());
    }
}
