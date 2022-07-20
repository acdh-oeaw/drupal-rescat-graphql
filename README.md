# drupal-rescat-graphql
Resource Catalog Drupal GraphQL plugin

## ingest person example

mutation{
  createPerson(data: { title: "test person ingest"}) {
    ... on Person {
      id
      title
    }
  }
}
