# Knowolo

Compile (factual) knowledge as a class for easier usage.

**Status:** Under heavy development, Pre-Alpha

But you should have a look into the [examples](./examples/) folder (see demo.php).
They already show what this approach is able to accomplish.

## License

MIT, see [LICENSE](./LICENSE).

## TODO list

### 0.1 (proof-of-concept)

* [x] attach meta data to generated PHP class (title, description, license, project page ...)
* `KnowledgeEntityInteface`
  * [ ] add `getSummary()`
* `KnowledgeModuleInterface`
  * [ ] add `getSynonyms` - get synonyms for a given term
    * use `http://www.geneontology.org/formats/oboInOwl#hasExactSynonym` (e.g. https://bioportal.bioontology.org/ontologies/KTAO/?p=summary)
  * [ ] add `getPropertiesOfClass` - get properties for a given class
* [ ] Fix getNarrowerTerms/getBroaderTerms returns list in wrong language: `echo PHP_EOL.'or as a list of strings: '.implode(', ', $module->getNarrowerTerms('Agricultural policy', 'en')->asListOfTitles());`
