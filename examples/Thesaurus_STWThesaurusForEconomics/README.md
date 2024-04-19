# Thesaurus example (using STW Thesaurus for Economics (Standard-Thesaurus Wirtschaft))

**Web page of the thesaurus:** https://zbw.eu/stw/version/latest/download/about.en.html

**Source file in RDF/Turtle format:** https://zbw.eu/stw/version/latest/download/stw.ttl.zip

## What is a thesaurus?

> *In the context of information retrieval, a thesaurus (plural: "thesauri") is a form of controlled vocabulary that seeks to dictate semantic manifestations of metadata in the indexing of content objects. A thesaurus serves to minimise semantic ambiguity by ensuring uniformity and consistency in the storage and retrieval of the manifestations of content objects.*

Source: https://en.wikipedia.org/wiki/Thesaurus_(information_retrieval)

## Generated knowledge module

Generated PHP file with knowledge module: [STWThesaurusForEconomics.php](./STWThesaurusForEconomics.php)

`STWThesaurusForEconomics.php` contains a ready to use instance of [KnowledgeModuleInterface.php](./../../scripts/src/KnowledgeModuleInterface.php).
This instance represents a huge chunk of the **STW Thesaurus for Economics**.
You can use various functions to access the content of the thesaurus without the need to know what RDF, Turtle, triples etc. are.
Our API is very simple and easy to use without prior knowledge: [KnowledgeModuleInterface.php](./../../scripts/src/KnowledgeModuleInterface.php)

**Example code:**

```php
// Load a PHP class instance, which is ready to use!
// see demo.php for further information
$module = require __DIR__.'/STWThesaurusForEconomics.php';

// count amount of terms stored
echo PHP_EOL.'Knowledge module contains '.count($module->getTerms()).' terms';

// get a list of narrower terms for a given term
echo PHP_EOL.'Agricultural policy (narrower terms):';
foreach ($module->getNarrowerTerms('Agricultural policy', 'en') as $term) {
    echo PHP_EOL.' - '.$term->getName('en');
}
```

This code will output something like:

```
Knowledge module contains 7633 terms

Agricultural policy (narrower terms):
 - Green revolution
 - Agricultural subsidy
 - Agricultural policy
 - Common market organization
 - Agrarian reform
 [...]
```

## Class generation

Use the following command to generate this class:

```bash
php scripts/bin/knowolo know:generate-serialized-php-code \
    examples/STWThesaurusForEconomics/stw.ttl \
    examples/STWThesaurusForEconomics/knowolo.json \
    > examples/STWThesaurusForEconomics/STWThesaurusForEconomics.php
```
