# Knowolo

Compile (factual) knowledge as a class for easier usage.

## License

MIT, see [LICENSE](./LICENSE).

## TODO list

### 0.1 (proof-of-concept)

* [ ] Fix getNarrowerTerms/getBroaderTerms returns list in wrong language: `echo PHP_EOL.'or as a list of strings: '.implode(', ', $module->getNarrowerTerms('Agricultural policy', 'en')->asListOfTitles());`
* `KnowledgeModuleInterface`
  * [ ] add `getSynonyms` - get synonyms for a given term
  * [ ] add `getPropertiesOfClass` - get properties for a given class
* [ ] attach meta data to generated PHP class (title, description, license, project page ...)
