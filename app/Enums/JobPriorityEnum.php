<?php

namespace App\Enums;

use ArchTech\Enums\Options;

/**
 * Enum for job priority levels.
 *
 * This enum provides a set of predefined values for job priorities, allowing for easy switching between different levels.
 */
enum JobPriorityEnum: int
{
    // This allows for easy iteration and manipulation of the enum values in a more convenient way than raw switch statements or if-else chains.
    // @see https://github.com/archtechx/enums
    use Options;

    case Low = 0;
    case Medium = 1;
    case High = 2;
}
