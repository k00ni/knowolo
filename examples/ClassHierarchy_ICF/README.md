# Example for class hierarchy (using International Classification of Functioning, Disability and Health (ICF))

Here is an example for a class hierarchy using the International Classification of Functioning, Disability and Health (ICF) as data source.
The ICF is provided as an ontology in a machine readable format (ICF_1.0.2_2012-08-05.owl).

**Project page of the ontology:** https://github.com/icdo/ICF

**Short summary of the ICF:**

> *The International Classification of Functioning, Disability and Health, known more commonly as **ICF**, is a classification of health and health-related domains. These domains are classified from body, individual and societal perspectives by means of two lists: a list of body functions and structure, and a list of domains of activity and participation. Since an individual's functioning and disability occurs in a context, the ICF also includes a list of environmental factors.* - (Source: https://bioportal.bioontology.org/ontologies/ICF/?p=summary)

## Class generation

Use the following command to generate this class:

```bash
php bin/knowolo know:generate-serialized-php-code \
    examples/ClassHierarchy_ICF/ICF_1.0.2_2012-08-05.owl \
    examples/ClassHierarchy_ICF/knowolo.json \
    > examples/ClassHierarchy_ICF/ICF.php
```
