<?php

declare(strict_types=1);

namespace Knowolo\DefaultImplementation;

use Knowolo\Exception;
use Knowolo\KnowledgeEntityInterface;

use function Knowolo\isEmpty;

class KnowledgeEntity implements KnowledgeEntityInterface
{
    /**
     * @var array<string|null,non-empty-string>
     */
    private array $namePerLanguage = [];

    /**
     * @var non-empty-string
     */
    private string $id;

    /**
     * @param array<string|null,non-empty-string> $namePerLanguage Structure looks like: ['en' => 'name', ...]
     * @param non-empty-string $id
     *
     * @throws \Knowolo\Exception if $id is empty
     */
    public function __construct(array $namePerLanguage, string $id)
    {
        if (isEmpty($id)) {
            throw new Exception('ID can not be empty.');
        }

        $this->id = $id;

        $this->namePerLanguage = $namePerLanguage;
    }

    /**
     * @throws \Knowolo\Exception if there are no names or no name for given language available.
     */
    public function getName(string|null $language = null): string
    {
        if (isEmpty($language) && 0 < count($this->namePerLanguage)) {
            return array_values($this->namePerLanguage)[0];
        } elseif (
            false === isEmpty($language)
            && true === isset($this->namePerLanguage[$language])
        ) {
            return $this->namePerLanguage[$language];
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
        $namePerLanguage = [];
        foreach ($this->namePerLanguage as $language => $name) {
            $namePerLanguage[] = '\''.$language.'\' => \''.addslashes($name).'\'';
        }

        return 'new \\'.self::class.'(['.implode(', ', $namePerLanguage).'], \''.$this->getId().'\')';
    }
}
