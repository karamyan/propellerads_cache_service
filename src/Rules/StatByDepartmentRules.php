<?php

declare(strict_types=1);

namespace App\Rules;


use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\DateTime;


class StatByDepartmentRules
{
    /**
     * @return Collection
     */
    public static function getStatRules(): Collection
    {
        return new Collection([
            'datamarts' => [
                new Type('array'),
                new NotNull(message: 'datamarts field is required'),
                new Choice(['choices' => ['stats_dep_a', 'stats_dep_b', 'stats_dep_c']], multiple: true)
            ],
            'date_time_from' => self::getDateTimeRules('date_time_from'),
            'date_time_to' => self::getDateTimeRules('date_time_to'),
        ]);
    }

    /**
     * @return Collection
     */
    public static function getStatCalculateRules(): Collection
    {
        return new Collection([
            'datamarts' => [
                new Type('string'),
                new NotNull(message: 'datamarts field is required'),
                new Choice(['choices' => ['stats_dep_a', 'stats_dep_b', 'stats_dep_c']])
            ],
            'date_time' => self::getDateTimeRules('date_time'),
        ]);
    }

    /**
     * @param string $field
     * @return array
     */
    private static function getDateTimeRules(string $field): array
    {
        return [
            new Type('string'),
            new NotNull(message: $field . ' field is required'),
            new DateTime(format: "Y-m-d H:i")
        ];
    }
}
