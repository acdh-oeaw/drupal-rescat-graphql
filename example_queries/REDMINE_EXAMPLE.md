# 1. Ingest Project:
### RAW data:

actors:
  - personId: 2
    role: ProjectLeader
  - personId: 3
    role: Editor
  - personId: 4
    role: Editor
  - personId: 5
    role: Editor
  - personId: 6
    role: Editor
drupalUser: user2
endDate: '2022-02-28'
id: 2
name: 'Digital edition of the Karl Kraus Legal Papers (OFWF31138) '
redmineIssueId: 6086
startDate: '2018-09-01'

### Graphql:
```
mutation AddProject {
  createProject(
    data: {headline: "Digital edition of the Karl Kraus Legal Papers (OFWF31138)", redmineId: 6086, startDate: "2018-09-01", endDate: "2022-02-28"}
  ) {
    id
  }
}
```
Response PROJECT ID: 95
_____________________________________________

# 2. Ingest Dataset

### RAW data:
actors:
  - personId: 15
    role: ContactPerson
  - personId: 32
    role: Editor
  description: "Taxonomy used in legalkraus project #6086,\r\n\r\nOriginal location:\
    \ https://gitlab.com/acdh-oeaw/legalkraus/vocabs/-/tree/master\r\n\r\nContext:\
    \ Applied to material on https://www.kraus.wienbibliothek.at/\r\n\r\nContact:\
    \ internal, see Assignees\r\n\r\nUpdate workflow: Changes in vocabseditor, export\
    \ the SKOS (ttl and rdf) from there, add the date to the filename and put it on\
    \ `vocabs-acdh-dumps`-github - update Skosmos triplestore"
  id: 2
  name: legalkraus taxonomy
  projectId: 2
  redmineIssueId: 19895

### GraphQL:
```
mutation AddDataset {
  createDataset(
    data: {headline: "legalkraus taxonomy", redmineId: 19895, description: "axonomy used in legalkraus project #6086,\\r\\n\\r\\nOriginal location:\\     \\ https://gitlab.com/acdh-oeaw/legalkraus/vocabs/-/tree/master\\r\\n\\r\\nContext:\\     \\ Applied to material on https://www.kraus.wienbibliothek.at/\\r\\n\\r\\nContact:\\     \\ internal, see Assignees\\r\\n\\r\\nUpdate workflow: Changes in vocabseditor, export\\     \\ the SKOS (ttl and rdf) from there, add the date to the filename and put it on\\     \\ `vocabs-acdh-dumps`-github - update Skosmos triplestore"}
  ) {
    id
  }
}
```

Response DATASET ID: 96

__________

# 3. Ingest Dataset Instance (name is missing for the dataset Instace in the raw data)

### RAW DATA:
  datasetId: 2
  id: 2
  locationPath: https://github.com/acdh-oeaw/vocabs-acdh-dumps/tree/main/CulturalHistory/legalkraus
  state: `To be filled in by a harvester

### GraphQL:

```
mutation AddDatasetinstance {
  createDatasetInstance(
    data: {headline: "Legal Kraus Dataset 01", locationUri: "https://github.com/acdh-oeaw/vocabs-acdh-dumps/tree/main/CulturalHistory/legalkraus", harvestingStatus: "To be filled in by a harvester"}
  ) {
    id
  }
}
```
Response Dataset Instance ID: 97
________________________

# 4. Ingest persons:

## 4.1 Get Roles:
```
query GetRoles {
  taxonomies(limit: 30) {
    items {
      headline
      id
      name
    }
  }
}
```

## 4.2 Ingest Project Users:

user 1: personId: 2 (ingestd id: 98) / role: ProjectLeader (id: 20)
user 2: personId: 3 (ingestd id: 99) / role: Editor (id: 19)
user 3: personId: 4 (ingestd id: 100) / role: Editor  (id: 19)
user 4: personId: 5 (ingestd id: 101) / role: Editor  (id: 19)

### RAW DATA user 1:

id: 2
  identifiers:
  - id: https://redmine.acdh.oeaw.ac.at/users/122
    label: "Vanessa Hannesschl\xE4ger"
    type: redmine
  - id: vanessa.hannesschlaeger@oeaw.ac.at
    label: "Vanessa Hannesschl\xE4ger"
    type: email


### GraphQL: (identifiers are not working yet...)
```
mutation AddPerson1 {
  createPerson(data: {title: "Vanessa Hannesschl\\xE4ger"}) {
    id
    title
  }
}
```

etc...

## 4.3 Add Relation between user and project:

PROJECT ID: 95
user 1: personId: 2 (ingestd id: 98) / role: ProjectLeader (id: 20)
user 2: personId: 3 (ingestd id: 99) / role: Editor (id: 19)
user 3: personId: 4 (ingestd id: 100) / role: Editor  (id: 19)
user 4: personId: 5 (ingestd id: 101) / role: Editor  (id: 19)

### GraphqL:
```
mutation AddPersonRelation {
  createPersonRelation(data: {target_id: 98, parent_id: 95, relation_id: 20}) {
    id
  }
}
```
Relation Id: 163 / 164 / 165 / 166

## 4.4 Ingest Persons to Dataset and Person relations to dataset:

### RAW DATA:

id: 15
  identifiers:
  - id: https://redmine.acdh.oeaw.ac.at/users/18
    label: Matej Durco
    type: redmine
  - id: matej.durco@oeaw.ac.at
    label: Matej Durco
    type: email

  id: 32
  identifiers:
  - id: https://redmine.acdh.oeaw.ac.at/users/134
    label: Klaus Illmayer
    type: redmine
  - id: klaus.illmayer@oeaw.ac.at
    label: Klaus Illmayer
    type: email

Ingest the user as we did in the 4.2:
DATASET ID: 96
user 1: personId: 15 (ingestd id: 102) / role: ContactPerson (id: 15)
user 2: personId: 32 (ingestd id: 103) / role: Editor (id: 19)

### GraphQL:

```
mutation AddPersonRelation {
  createPersonRelation(data: {target_id: 102, parent_id: 96, relation_id: 15}) {
    id
  }
}
```
_________________________

# 5. Add Relation between Project and Dataset

DATASET ID: 96
PROJECT ID: 95

```
mutation AddDatasetRelation {
  createDatasetRelation(data: {target_id: 96, parent_id: 95}) {
    id
  }
}
```

# 6. Add Relation between Dataset and DatasetInstance

DATASET ID: 96
Dataset Instance ID: 97

```
mutation AddDatasetInstanceRelation {
  createDatasetInstanceRelation(data: {target_id: 97, parent_id: 96}) {
    id
  }
}
```

