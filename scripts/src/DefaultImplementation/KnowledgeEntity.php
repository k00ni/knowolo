<?php

declare(strict_types=1);

namespace Knowolo\DefaultImplementation;

use Knowolo\Exception;
use Knowolo\KnowledgeEntityInterface;

use function Knowolo\isEmpty;

class KnowledgeEntity implements KnowledgeEntityInterface
{
    /**
     * @var array<string,non-empty-string>
     */
    private array $titlePerLanguage = [];

    /**
     * @var non-empty-string
     */
    private string $id;

    /**
     * @param array<string,non-empty-string> $titlePerLanguage Structure looks like: ['en' => 'name', ...]
     * @param non-empty-string $id
     *
     * @throws \Knowolo\Exception if $id is empty
     */
    public function __construct(array $titlePerLanguage, string $id)
    {
        if (isEmpty($id)) {
            throw new Exception('ID can not be empty.');
        }
        $this->id = $id;

        if (0 == count($titlePerLanguage)) {
            throw new Exception('titlePerLanguage has no elements');
        } elseif (1 == count($titlePerLanguage) && isEmpty(array_values($titlePerLanguage)[0])) {
            throw new Exception('titlePerLanguage has one element, which is empty');
        }
        $this->titlePerLanguage = $titlePerLanguage;
    }

    /**
     * @throws \Knowolo\Exception if there are no names or no name for given language available.
     */
    public function getTitle(string|null $language = null): string
    {
        if (isEmpty($language) && 0 < count($this->titlePerLanguage)) {
            return array_values($this->titlePerLanguage)[0];
        } elseif (
            false === isEmpty($language)
            && true === isset($this->titlePerLanguage[$language])
        ) {
            return $this->titlePerLanguage[$language];
        } else {
            throw new Exception('No names available or no name for given language.');
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function asPhpCodeNew(): string
    {
        $titlePerLanguage = [];
        foreach ($this->titlePerLanguage as $language => $name) {
            $titlePerLanguage[] = '\''.$language.'\' => \''.addslashes($name).'\'';
        }

        return 'new \\'.self::class.'(['.implode(', ', $titlePerLanguage).'], \''.$this->getId().'\')';
    }
}
