## drupal resource catalog
The GraphQL Query plugin for the Resource Catalog.

## Available queries:
###### Select Queries: 
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
- Taxonomies (params: name, limit, offset) - Query the relations (Taxonomies) (f.e. "has contributor", etc...)


###### Create Queries: 
- Person (params: title, description) - Ingest the base data for the Person node type
- Institution (params: title, identifiers) - Ingest the base data for the Person node type
- Dataset (params: headline, description) - Ingest the base data for the Dataset node type
- DatasetInstance (params: headline, description, harvestingStatus, lastHarvestDate, license, locationTitle, locationUri, size) - Ingest the base data for the DatasetInstance node type
- Project (params: headline, description, redmineId, startDate, endDate) - Ingest the base data for the Project node type
- Person Relation (params: parent_id, target_id, relation_id) - Ingest the relation between the Person and the Project and also between the Person and Taxonomy ('has contributor', etc...). 
The parent_id is the project node id, target_id the Person node id, relation_id is the relation id. (You can fetch the ids from the PersonsTaxonomy)
- Institution Relation (params: parent_id, target_id, relation_id) - Ingest the relation between the Institution and the Project and also between the Institution and Taxonomy ('has contributor', etc...). 
The parent_id is the project node id, target_id the Institution node id, relation_id is the relation id. (You can fetch the ids from the InstitutionsTaxonomy) 
- Dataset Relation (params: parent_id, target_id) - Ingest the relation between the Dataset and the Project. 
The parent_id is the project node id, target_id the Dataset node id. - Not Working
- DatasetInstance Relation (params: parent_id, target_id) - Ingest the relation between the Dataset and the DatasetInstance. 
The parent_id is the Dataset node id, target_id the DatasetInstance node id. - Not Working
- Taxonomy - not working

###### Update Queries: 
- Person (params: id, title, identifiers) - update the Person node
- Institution - (params: id, title, identifiers) - update the Institution node
- Dataset - (params: id, headine, description) - update the Dataset node
- DatasetInstance - (params: id, headine, description) - update the DatasetInstance node (Other fields are not working yet, we have to clarify the necessary fields)
- Project - (params: id, headine, description) - update the Project node (Other fields are not working yet, we have to clarify the necessary fields)
- Person Relation  - (params: parent_id, target_id, relation_id) - Update the person relation based on the Node Id and the person id.
- Institution Relation  - not working
- Dataset Relation - (params: parent_id, target_id) - Update Relation between Project and Dataset - not working
- DatasetInstance Relation - not working
- Taxonomy - not working

###### Delete Queries: 
- Person (params: id) - deletes the node type Person based on the node id
- Institution - (params: id) - deletes the node type Institution based on the node id
- Dataset - (params: id) - deletes the node type Dataset based on the node id
- DatasetInstance - (params: id) - deletes the node type DatasetInstance based on the node id
- Project - (params: id) - deletes the node type Project based on the node id
- Person Relation  - (params: id, target_id) - deletes the Person relation from the node. Id = the node id for and the target id is the drupal internal target id for the relation.
- Institution Relation  - not working
- Dataset Relation - not working
- DatasetInstance Relation - not working
- Taxonomy - not working


## Permissions:

You have to define the permission inside the rescat_graphql.permissions.yml file and then you can implement it in the requests file. f.e.: [https://github.com/acdh-oeaw/drupal-rescat-graphql/blob/master/src/Plugin/GraphQL/DataProducer/CreatePerson.php#L78]