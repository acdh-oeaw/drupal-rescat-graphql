# Examples

###### query project
```
query MyQuery {
  project(id: 9) {
    id
    headline
    affilatedInstitutions {
      description
      id
      identifiers
      title
    }
    contributors {
      id
      identifiers
      title
    }
    datasets {
      datasetInstance {
        contributors {
          id
          identifiers
          title
        }
        description
        harvestingStatus
        headline
        id
        lastHarvestDate
        license
        locationTitle
        locationUri
        size
      }
      id
      title
    }
    description
    endDate
    principalInvestigators {
      id
      identifiers
      title
    }
    redmineId
    startDate
  }
}
```


## Ingest project/person/dataset/dataset instance/institution

###### Institution
```
mutation InstitutionIngest {
  createInstitution(data: {title: "ACDH-CH", identifiers: "https://www.oeaw.ac.at/acdh/acdh-ch-home"}) {
    description
    id
    identifiers
    title
  }
}

```

###### Person
```
mutation PersonIngest {
  createPerson(data: {title: "Ingestion User", description: "Example User for ingestion"}) {
    id
    identifiers
    title
  }
}
```

###### Project
```
mutation ProjectIngest {
  createProject(
    data: {headline: "Example Project", description: "example project description", redmineId: 10}
  ) {
    id
    headline
  }
}
```

###### Dataset
```
mutation DatasetIngest {
  
}
```

###### DatasetInstance
```
mutation DatasetInstanceIngest {
  
}
```


## Ingest Relations

###### PersonRelation Ingest
Parent_id is the node id which will contains the relation and the target_id is the actual Person node id. The relation id is the 
Id of the person relation (f.e.: 8 has contributor) -> You can find them in the Query Taxonomies section
```
mutation PersonRelationMutation {
  createPersonRelation(data: {parent_id: "73", target_id: "11", relation_id: "8"}) {
    id
    uuid
  }
}
```


## Query Paragraphs
```
query MyQuery {
  project(id: 49) {
    id
    headline
    institutionRelations {
      id
      ... on InstitutionRelation {
        id
        uuid
        institution {
          id
          title
        }
      }
    }
    personRelations {
      id
      ... on PersonRelation {
        id
        uuid
        person {
          id
          title
        }
      }
    }
  }
}


```

## Query Taxonomies
We have taxonomies like connections between Person and Project -> has contributor, etc...


```
query TaxonomyQuery {
  taxonomy(name: "has contributor") {
    items {
      id
      name
    }
  }
}

```


## Delete

###### Delete Person
Where the id the person node id.

```
mutation DeleteMutation {
  deletePerson(data: {id: 78}) {
    id
  }
}
```






