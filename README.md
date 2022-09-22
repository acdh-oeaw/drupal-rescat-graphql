## drupal resource catalog
The GraphQL Query plugin for the Resource Catalog.

##Available queries:
###Select Queries: 
- Person (params: id) - Query the base Person Data
- Persons (params: title, limit, offset) - Query Persons base data 
- Project (params: id) - Query the Project and the relations data
- Projects (params: title, limit, offset) -Query the Projects and the relations data
- Institution (params: id) - Query the base Institution Data
- Institutions (params: title, limit, offset) - Query the base Institutions Data
- Dataset (params: id) - Query the Dataset (only the non paragraph instance fetching is working atm.)
- Datasets (params: title, limit, offset) - Query the Datasets (only the non paragraph instance fetching is working atm.)
- DatasetInstance (params: id) - Query the DatasetInstance (only the non paragraph instance fetching is working atm.)
- DatasetInstances (params: title, limit, offset) - Query the DatasetInstances (only the non paragraph instance fetching is working atm.)
- PersonTaxonomy - not working
- PersonsTaxonomy (params: name, limit, offset) - Query the relations (Taxonomies) for the Node Person types (f.e. "has contributor", etc...)
- InstitutionTaxonomy - not working
- InstitutionsTaxonomy (params: name, limit, offset) - not working - Query the relations (Taxonomies) for the Node Institution types (f.e. "has contributor", etc...)
If we will use paragraphs for the Dataset and DatasetInstance then we need the Taxonomy Queries for these node types too.
- DatasetTaxonomy - not working
- DatasetsTaxonomy (params: name, limit, offset) - not working 
- DatasetInstanceTaxonomy - not working
- DatasetInstancesTaxonomy (params: name, limit, offset) - not working 


###Create Queries: 
- Person (params: title, description) - Ingest the base data for the Person node type
- Institution (params: title, identifiers) - Ingest the base data for the Person node type
- Dataset (params: headline, description) - Ingest the base data for the Dataset node type
- DatasetInstance (params: headline, description, harvestingStatus, lastHarvestDate, license, locationTitle, locationUri, size) - Ingest the base data for the DatasetInstance node type
- Project (params: headline, description, redmineId, startDate, endDate) - Ingest the base data for the Project node type
- Person Relation (params: parent_id, target_id, relation_id) - Ingest the relation between the Person and the Project and also between the Person and Taxonomy ('has contributor', etc...). 
The parent_id is the project node id, target_id the Person node id, relation_id is the relation id. (You can fetch the ids from the PersonsTaxonomy)
- Institution Relation (params: parent_id, target_id, relation_id) - Ingest the relation between the Institution and the Project and also between the Institution and Taxonomy ('has contributor', etc...). 
The parent_id is the project node id, target_id the Institution node id, relation_id is the relation id. (You can fetch the ids from the InstitutionsTaxonomy) - Not Working
- Dataset Relation (params: parent_id, target_id) - Ingest the relation between the Dataset and the Project. 
The parent_id is the project node id, target_id the Dataset node id. - Not Working
- DatasetInstance Relation (params: parent_id, target_id) - Ingest the relation between the Dataset and the DatasetInstance. 
The parent_id is the Dataset node id, target_id the DatasetInstance node id. - Not Working
- Person Taxonomy - not working
- Institution Taxonomy - not working

###Update Queries: 
- Person (params: id, title, identifiers) - update the person node
- Institution - not working
- Dataset - not working
- DatasetInstance - not working
- Project - not working
- Person Relation  - not working
- Institution Relation  - not working
- Dataset Relation - not working
- DatasetInstance Relation - not working
- Person Taxonomy - not working
- Institution Taxonomy - not working

###Delete Queries: 
- Person (params: id) - deletes the node type person based on the node id
- Institution - not working
- Dataset - not working
- DatasetInstance - not working
- Project - not working
- Person Relation  - not working
- Institution Relation  - not working
- Dataset Relation - not working
- DatasetInstance Relation - not working
- Person Taxonomy - not working
- Institution Taxonomy - not working

#Examples

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


###Delete

##Delete Person
Where the id the person node id.

```
mutation DeleteMutation {
  deletePerson(data: {id: 78}) {
    id
  }
}
```


