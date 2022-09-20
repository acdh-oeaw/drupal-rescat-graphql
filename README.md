## drupal resource catalog

## query project
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



## Ingest project/person/dataset/dataset instance/institution

### Institution
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

### Person
```
mutation PersonIngest {
  createPerson(data: {title: "Ingestion User", description: "Example User for ingestion"}) {
    id
    identifiers
    title
  }
}
```

### Project
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

### Dataset
```
mutation DatasetIngest {
  
}
```

### DatasetInstance
```
mutation DatasetInstanceIngest {
  
}
```


## Ingest Relations

### PersonRelation Ingest
Parent_id is the node id which will contains the relation and the target_id is the actual Person node id.
```
mutation PersonRelationMutation {
  createPersonRelation(data: {parent_id: "73", target_id: "11"}) {
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

##Query Taxonomies
We have taxonomies like connections between Person and Project -> has contributor, etc...


```
query PersonTaxonomyQuery {
  personstaxonomy(name: "has contributor") {
    items {
      id
      name
    }
  }
}

```



