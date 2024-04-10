# Example STW Thesaurus for Economics (Standard-Thesaurus Wirtschaft)

**Web page of the thesaurus:** https://zbw.eu/stw/version/latest/download/about.en.html

**Source file in RDF/Turtle format:** https://zbw.eu/stw/version/latest/download/stw.ttl.zip

## What is a thesaurus?

> *In the context of information retrieval, a thesaurus (plural: "thesauri") is a form of controlled vocabulary that seeks to dictate semantic manifestations of metadata in the indexing of content objects. A thesaurus serves to minimise semantic ambiguity by ensuring uniformity and consistency in the storage and retrieval of the manifestations of content objects.*

Source: https://en.wikipedia.org/wiki/Thesaurus_(information_retrieval)

## Generated knowledge module

- reference to file
- short summary
- code examples how to use it

## Class generation

Use the following command to generate the class:

```bash
php scripts/bin/knowolo know:generate-as-plain-php ./examples/STWThesaurusForEconomics/stw.ttl > ./examples/STWThesaurusForEconomics/STWThesaurusForEconomics.php
```
