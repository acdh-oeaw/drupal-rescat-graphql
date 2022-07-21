## drupal resource catalog

## ingest person example

mutation{
  createPerson(data: { title: "test person ingest"}) {
    ... on Person {
      id
      title
    }
  }
}


## ingest institution example
mutation{
  createInstitution(data: { title: "test person ingest"}) {
    ... on Institution {
      id
      title
    }
  }
}

## ingest dataset instance
mutation{
  createDatasetInstance(data: { headline: "test DTI 2", description: "DTi1 description", 
    harvestingStatus: "harvesting..", lastHarvestDate: "2022-02-14 00:00:00", 
    license: "license test", locationTitle: "location title test", locationUri: "location uri test",
  	size: 929921, contributors: { id: 10, title: "klaus"}}) {
    ... on DatasetInstance {
      id
      headline
      description
      harvestingStatus
      lastHarvestDate
      license
      locationTitle
      locationUri
      size
      contributors {
        id
        title
      }
    }
  }
}