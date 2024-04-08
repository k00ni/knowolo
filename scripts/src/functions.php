<?php

declare(strict_types=1);

namespace Knowolo;

/**
 * empty() is not enough to check if something is really empty.
 * This function takes care of the edge cases.
 *
 * @api
 * @see https://stackoverflow.com/questions/718986/checking-if-the-string-is-empty
 */
function isEmpty(string|null $input): bool
{
    if (null === $input) {
        return true;
    } else { // its a string
        $input = trim($input);
        $input = (string) preg_replace('/\s/', '', $input);

        return 0 == strlen($input);
    }
}
