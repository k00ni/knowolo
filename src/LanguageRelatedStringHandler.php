<?php

declare(strict_types=1);

namespace Knowolo;

use Knowolo\Exception\KnowoloException;
use Knowolo\Exception\StringNotFoundException;

/**
 * @api
 */
class LanguageRelatedStringHandler
{
    /**
     * @var array<string,non-empty-string>
     */
    private array $stringPerLanguage = [];

    /**
     * @param array<string,non-empty-string> $stringPerLanguage
     *
     * @throws \Knowolo\Exception\KnowoloException
     */
    public function __construct(array $stringPerLanguage)
    {
        if (0 == count($stringPerLanguage)) {
            throw new KnowoloException('titlePerLanguage has no elements');
        } elseif (1 == count($stringPerLanguage) && isEmpty(array_values($stringPerLanguage)[0])) {
            throw new KnowoloException('titlePerLanguage has one element, which is empty');
        }

        // validate data
        foreach ($stringPerLanguage as $lang => $str) {
            if (isEmpty($str)) {
                throw new KnowoloException('Empty string found for language: '.$lang);
            }
        }

        $this->stringPerLanguage = $stringPerLanguage;
    }

    /**
     * @return non-empty-string
     *
     * @throws \Knowolo\Exception\StringNotFoundException
     */
    public function getString(string|null $language = null): string
    {
        // no language given, but strings available
        if (isEmpty($language) && 0 < count($this->stringPerLanguage)) {
            return array_values($this->stringPerLanguage)[0];
        } elseif (
            false === isEmpty($language)
            && true === isset($this->stringPerLanguage[$language])
        ) {
            // language given and related string is available
            return $this->stringPerLanguage[$language];
        } elseif (
            false === isEmpty($language)
            && 0 < count($this->stringPerLanguage)
            && false === isset($this->stringPerLanguage[$language])
        ) {
            // language given but related string is not available
            return array_values($this->stringPerLanguage)[0];
        } else {
            throw new StringNotFoundException();
        }
    }

    /**
     * @return array<string,non-empty-string>
     */
    public function getData(): array
    {
        return $this->stringPerLanguage;
    }
}
