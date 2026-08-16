<?php

namespace Shetabit\Stampable\Exceptions;

use InvalidArgumentException;

class StampNotFoundException extends InvalidArgumentException
{
    /**
     * @param list<string> $availableStamps
     */
    public static function forStamp(string $stampName, array $availableStamps) : self
    {
        return new self(sprintf(
            'The stamp [%s] is not defined. Available stamps: %s.',
            $stampName,
            $availableStamps === [] ? 'none' : implode(', ', $availableStamps),
        ));
    }
}
