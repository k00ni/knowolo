<?php

declare(strict_types=1);

namespace Knowolo\DefaultImplementation;

use Knowolo\Exception\StringNotFoundException;
use Knowolo\KnowledgeEntityInterface;
use Knowolo\LanguageRelatedStringHandler;
use ValueError;

use function Knowolo\isEmpty;

/**
 * @api
 */
class KnowledgeEntity implements KnowledgeEntityInterface
{
    private LanguageRelatedStringHandler $titlePerLanguage;

    /**
     * @var non-empty-string
     */
    private string $id;

    /**
     * @param non-empty-string $id
     *
     * @throws \ValueError
     */
    public function __construct(LanguageRelatedStringHandler $titlePerLanguage, string $id)
    {
        if (isEmpty($id)) {
            throw new ValueError('ID can not be empty.');
        }
        $this->id = $id;

        $this->titlePerLanguage = $titlePerLanguage;
    }

    public function getTitle(string|null $language = null): string
    {
        try {
            return $this->titlePerLanguage->getString($language);
        } catch (StringNotFoundException) {
            return $this->getId();
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function asPhpCodeNew(): string
    {
        $titlePerLanguage = [];
        foreach ($this->titlePerLanguage->getData() as $language => $name) {
            $titlePerLanguage[] = '\''.$language.'\' => \''.addslashes($name).'\'';
        }

        return 'new \\'.self::class.'(['.implode(', ', $titlePerLanguage).'], \''.$this->getId().'\')';
    }
}
