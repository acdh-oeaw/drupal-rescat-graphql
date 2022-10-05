<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Schema;

use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\Plugin\GraphQL\Schema\SdlSchemaPluginBase;
use Drupal\rescat_graphql\Wrappers\QueryConnection;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;
use GraphQL\Error\Error;

/**
 * @Schema(
 *   id = "rescat",
 *   name = "Resource Catalog schema"
 * )
 */
class ResCatSchema extends SdlSchemaPluginBase {

    /**
     * {@inheritdoc}
     */
    public function getResolverRegistry() {
        $builder = new ResolverBuilder();
        $registry = new ResolverRegistry();

        /**
         * Mutations
         */
        $this->addMutations($registry, $builder);
       
        $registry->addTypeResolver('NodeInterface', function ($value) {
            if ($value instanceof NodeInterface) {
                switch ($value->bundle()) {
                    case 'dataset': return 'Dataset';
                    case 'dataset_instance': return 'DatasetInstance';
                    case 'institution': return 'Institution';
                    case 'person': return 'Person';
                    case 'taxonomy': return 'Taxonomy';
                    case 'project': return 'Project';
                }
            }
            throw new Error('Could not resolve content type.');
        });
        
        $this->addQueryFields($registry, $builder);
        $this->addDatasetFields($registry, $builder);
        $this->addDatasetInstanceFields($registry, $builder);
        $this->addInstitutionFields($registry, $builder);
        $this->addPersonFields($registry, $builder);
        $this->addTaxonomyFields($registry, $builder);
        $this->addProjectFields($registry, $builder);
        $this->addPersonRelationsFields($registry, $builder);
        
        // Re-usable connection type fields.
        $this->addConnectionFields('DatasetConnection', $registry, $builder);
        $this->addConnectionFields('DatasetInstanceConnection', $registry, $builder);
        $this->addConnectionFields('InstitutionConnection', $registry, $builder);
        $this->addConnectionFields('PersonConnection', $registry, $builder);
        $this->addConnectionFields('ProjectConnection', $registry, $builder);
        $this->addConnectionFields('TaxonomyConnection', $registry, $builder);
        $this->addConnectionFields('PersonRelationsConnection', $registry, $builder);

        return $registry;
    }

    /**
     * include the mutations for the data manipulation
     * @param ResolverRegistry $registry
     * @param ResolverBuilder $builder
     * @param string $type
     * @param string $producer
     */
    private function includeMutations(ResolverRegistry &$registry, ResolverBuilder &$builder, string $type, string $producer) {
        $registry->addFieldResolver('Mutation', $type,
                $builder->produce($producer)
                        ->map('data', $builder->fromArgument('data'))
        );
    }
    
    /**
     *  fetch the base values
     * @param ResolverRegistry $registry
     * @param ResolverBuilder $builder
     * @param string $type
     * @param string $field
     * @param string $producer
     */
    private function getValueFromParent(ResolverRegistry &$registry, ResolverBuilder &$builder, string $type, string $field, string $producer) {
        $registry->addFieldResolver($type, $field,
                $builder->produce($producer)
                        ->map('entity', $builder->fromParent())
        );
    }
    
    /**
     * Get base value by field
     * @param ResolverRegistry $registry
     * @param ResolverBuilder $builder
     * @param string $type
     * @param string $field
     * @param string $producer
     * @param string $fromValue
     */
    private function getValueByField(ResolverRegistry &$registry, ResolverBuilder &$builder, string $type, string $field, string $producer, string $fromValue) {
        $registry->addFieldResolver($type, $field,
                $builder->produce($producer)
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue($fromValue))
        );
    }
    
    
    /**
     * get value from entity:node
     * @param ResolverRegistry $registry
     * @param ResolverBuilder $builder
     * @param string $type
     * @param string $field
     * @param string $producer
     * @param string $fromValue
     */
    private function getValueByEntityNode(ResolverRegistry &$registry, ResolverBuilder &$builder, string $type, string $field, string $producer, string $fromValue) {
         $registry->addFieldResolver($type, $field,
                $builder->produce($producer)
                        ->map('type', $builder->fromValue('entity:node'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue($fromValue))
        );
    }
    
    /**
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addDatasetFields(ResolverRegistry $registry, ResolverBuilder $builder) {
        
        $this->getValueFromParent($registry, $builder, 'Dataset', 'id', 'entity_id');
        $this->getValueFromParent($registry, $builder, 'Dataset', 'uuid', 'entity_uuid');
         
        
        $registry->addFieldResolver('Dataset', 'datasetInstanceRelations',
                $builder->produce('entity_reference_revisions')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_dataset_instance_relations'))
        );
     
        $registry->addTypeResolver('Paragraph', function ($value) {
            if ($value instanceof Paragraph) {
                switch ($value->bundle()) {
                    case 'dataset_instance_relations': return 'DatasetInstanceRelation';
                }
            }
            //https://github.com/drupal-graphql/graphql/pull/968
            throw new Error('Could not resolve Paragraph type. ' . $value->bundle());
        });
        
        
        $registry->addFieldResolver('DatasetInstanceRelation', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('DatasetInstanceRelation', 'uuid',
                $builder->produce('entity_uuid')
                        ->map('entity', $builder->fromParent())
        );
       
        $registry->addFieldResolver('DatasetInstanceRelation', 'datasetInstance',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_dataset_instance_relation'))
        );
        
        //$this->getValueByField($registry, $builder, 'Dataset', 'datasetInstance', 'entity_reference', 'field_dataset_instance_relations' );
        $this->getValueByEntityNode($registry, $builder, 'Dataset', 'title', 'property_path', 'title.value');
        $this->getValueByEntityNode($registry, $builder, 'Dataset', 'redmineId', 'property_path', 'field_redmineid.value');
        $this->getValueByEntityNode($registry, $builder, 'Dataset', 'projectId', 'property_path', 'field_projectid.value');
        $this->getValueByEntityNode($registry, $builder, 'Dataset', 'description', 'property_path', 'field_description.value');
    }

    /**
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addDatasetInstanceFields(ResolverRegistry $registry, ResolverBuilder $builder) {
        
        $this->getValueFromParent($registry, $builder, 'DatasetInstance', 'id', 'entity_id');
        $this->getValueFromParent($registry, $builder, 'DatasetInstance', 'headline', 'entity_label');
       
        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'description', 'property_path', 'body.value');
        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'lastHarvestDate', 'property_path', 'field_last_harvest_date.value');
        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'harvestingStatus', 'property_path', 'field_harvesting_status.value');
        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'license', 'property_path', 'field_license.value');
        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'size', 'property_path', 'field_size.value');

        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'locationUri', 'property_path', 'field_location.value');
        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'locationTitle', 'property_path', 'field_location.value');
    }

    /**
     * The base Institution node fields
     * 
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addInstitutionFields(ResolverRegistry $registry, ResolverBuilder $builder) {
        $this->getValueFromParent($registry, $builder, 'Institution', 'id', 'entity_id');
        $this->getValueFromParent($registry, $builder, 'Institution', 'title', 'entity_label');
        $this->getValueByEntityNode($registry, $builder, 'Institution', 'description', 'property_path', 'body.value');
        $this->getValueByEntityNode($registry, $builder, 'Institution', 'identifiers', 'property_path', 'field_identifiers.value');
    }

    protected function addPersonRelationsFields(ResolverRegistry $registry, ResolverBuilder $builder) {
        
         // Person relation
        $registry->addFieldResolver('PersonRelation', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('PersonRelation', 'uuid',
                $builder->produce('entity_uuid')
                        ->map('entity', $builder->fromParent())
        );
        

        $registry->addFieldResolver('PersonRelation', 'person',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_person'))
        );

        // Reading the relation of the person paragraph, pointing to a taxonomy
        $registry->addFieldResolver('PersonRelation', 'relation',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_relation'))
        );
        $registry->addFieldResolver('Relation', 'id',
          $builder->produce('entity_id')
            ->map('entity', $builder->fromParent())
        ); 
        $registry->addFieldResolver('Relation', 'name',
          $builder->produce('entity_label')
            ->map('entity', $builder->fromParent())
        );
    }
    
    /**
     * The Base Person Node fields
     * 
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addPersonFields(ResolverRegistry $registry, ResolverBuilder $builder) {
        $this->getValueFromParent($registry, $builder, 'Person', 'id', 'entity_id');
        $this->getValueByEntityNode($registry, $builder, 'Person', 'title', 'property_path', 'title.value');
        $this->getValueByEntityNode($registry, $builder, 'Person', 'identifiers', 'property_path', 'field_identifiers.value');
    }
    
    /**
     * The taxonomy fields like has contributor, etc
     * 
     * @param ResolverRegistry $registry
     * @param ResolverBuilder $builder
     */
    protected function addTaxonomyFields(ResolverRegistry $registry, ResolverBuilder $builder) {
        $this->getValueFromParent($registry, $builder, 'Taxonomy', 'id', 'entity_id');
        $this->getValueByEntityNode($registry, $builder, 'Taxonomy', 'title', 'property_path', 'title.value');
        $this->getValueByEntityNode($registry, $builder, 'Taxonomy', 'name', 'property_path', 'name.value');
        $this->getValueByEntityNode($registry, $builder, 'Taxonomy', 'identifiers', 'property_path', 'field_identifiers.value');
    }
    

    /**
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addProjectFields(ResolverRegistry $registry, ResolverBuilder $builder) {
        $this->getValueFromParent($registry, $builder, 'Project', 'id', 'entity_id');
        $this->getValueFromParent($registry, $builder, 'Project', 'headline', 'entity_label');
        $this->getValueByEntityNode($registry, $builder, 'Project', 'description', 'property_path', 'body.value');
        
        $this->getValueByEntityNode($registry, $builder, 'Project', 'startDate', 'property_path', 'field_start.value');
        $this->getValueByEntityNode($registry, $builder, 'Project', 'endDate', 'property_path', 'field_end.value');
        $this->getValueByEntityNode($registry, $builder, 'Project', 'redmineId', 'property_path', 'field_redmine_id.value');
      
        ///////////////// Relations //////////////////
        
        $registry->addFieldResolver('Project', 'personRelations',
                $builder->produce('entity_reference_revisions')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_person_relations'))
        );
        
        $registry->addFieldResolver('Project', 'institutionRelations',
                $builder->produce('entity_reference_revisions')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_institution_relations'))
        );
        
        $registry->addFieldResolver('Project', 'datasetRelations',
                $builder->produce('entity_reference_revisions')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_dataset_relations'))
        );
     
        $registry->addTypeResolver('Paragraph', function ($value) {
            if ($value instanceof Paragraph) {
                switch ($value->bundle()) {
                    case 'person_relations': return 'PersonRelation';
                    case 'dataset_relation': return 'DatasetRelation';
                    case 'institution_relations': return 'InstitutionRelation';
                    case 'dataset_instance_relations': return 'DatasetInstanceRelation';
                }
            }
            //https://github.com/drupal-graphql/graphql/pull/968
            throw new Error('Could not resolve Paragraph type. ' . $value->bundle());
        });

        // Person relation
        $registry->addFieldResolver('PersonRelation', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('PersonRelation', 'uuid',
                $builder->produce('entity_uuid')
                        ->map('entity', $builder->fromParent())
        );
        

        $registry->addFieldResolver('PersonRelation', 'person',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_person'))
        );

        // Reading the relation of the person paragraph, pointing to a taxonomy
        $registry->addFieldResolver('PersonRelation', 'relation',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_relation'))
        );
        
        $registry->addFieldResolver('Relation', 'id',
          $builder->produce('entity_id')
            ->map('entity', $builder->fromParent())
        ); 
        $registry->addFieldResolver('Relation', 'name',
          $builder->produce('entity_label')
            ->map('entity', $builder->fromParent())
        );

        //$this->createPersonTermFieldResolver($registry, $builder);
    
        // Institution Relation
        
        $registry->addFieldResolver('InstitutionRelation', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('InstitutionRelation', 'uuid',
                $builder->produce('entity_uuid')
                        ->map('entity', $builder->fromParent())
        );
       
        $registry->addFieldResolver('InstitutionRelation', 'institution',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_institution'))
        );

        //$this->createInstitutionsTermFieldResolver($registry, $builder);
       
        $registry->addFieldResolver('DatasetRelation', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('DatasetRelation', 'uuid',
                $builder->produce('entity_uuid')
                        ->map('entity', $builder->fromParent())
        );
       
        $registry->addFieldResolver('DatasetRelation', 'dataset',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_dataset_relation'))
        );
        
     
        
    }
    
   
    /**
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addQueryFields(ResolverRegistry $registry, ResolverBuilder $builder) {
        /* * * PROJECT ** */
        $registry->addFieldResolver('Query', 'project',
                $builder->produce('entity_load')
                        ->map('type', $builder->fromValue('node'))
                        ->map('bundles', $builder->fromValue(['project']))
                        ->map('id', $builder->fromArgument('id'))
        );

        $registry->addFieldResolver('Query', 'projects',
                $builder->produce('query_projects')
                        ->map('offset', $builder->fromArgument('offset'))
                        ->map('limit', $builder->fromArgument('limit'))
                        ->map('title', $builder->fromArgument('title'))
        );

        /*         * * PERSON** */
        $registry->addFieldResolver('Query', 'person',
                $builder->produce('entity_load')
                        ->map('type', $builder->fromValue('node'))
                        ->map('bundles', $builder->fromValue(['person']))
                        ->map('id', $builder->fromArgument('id'))
        );
        
        $registry->addFieldResolver('Query', 'persons',
                $builder->produce('query_persons')
                        ->map('offset', $builder->fromArgument('offset'))
                        ->map('limit', $builder->fromArgument('limit'))
                        ->map('title', $builder->fromArgument('title'))
        );
        
         $registry->addFieldResolver('Query', 'person_relations',
                $builder->produce('query_persons')
                        ->map('offset', $builder->fromArgument('offset'))
                        ->map('limit', $builder->fromArgument('limit'))
                        ->map('title', $builder->fromArgument('title'))
        );
        
        $registry->addFieldResolver('Query', 'taxonomies',
                $builder->produce('query_taxonomies')
                        ->map('offset', $builder->fromArgument('offset'))
                        ->map('limit', $builder->fromArgument('limit'))
                        ->map('name', $builder->fromArgument('name'))
        );

        /** * DATASET ** */
        $registry->addFieldResolver('Query', 'dataset',
                $builder->produce('entity_load')
                        ->map('type', $builder->fromValue('node'))
                        ->map('bundles', $builder->fromValue(['dataset']))
                        ->map('id', $builder->fromArgument('id'))
        );

        $registry->addFieldResolver('Query', 'datasets',
                $builder->produce('query_datasets')
                        ->map('offset', $builder->fromArgument('offset'))
                        ->map('limit', $builder->fromArgument('limit'))
                        ->map('title', $builder->fromArgument('title'))
        );

        /*** DATASET INSTANCE ** */
        $registry->addFieldResolver('Query', 'dataset_instance',
                $builder->produce('entity_load')
                        ->map('type', $builder->fromValue('node'))
                        ->map('bundles', $builder->fromValue(['dataset_instance']))
                        ->map('id', $builder->fromArgument('id'))
        );

        $registry->addFieldResolver('Query', 'dataset_instances',
                $builder->produce('query_dataset_instances')
                        ->map('offset', $builder->fromArgument('offset'))
                        ->map('limit', $builder->fromArgument('limit'))
                        ->map('title', $builder->fromArgument('title'))
        );

        /*         * * Institution ** */
        $registry->addFieldResolver('Query', 'institution',
                $builder->produce('entity_load')
                        ->map('type', $builder->fromValue('node'))
                        ->map('bundles', $builder->fromValue(['institution']))
                        ->map('id', $builder->fromArgument('id'))
        );

        $registry->addFieldResolver('Query', 'institutions',
                $builder->produce('query_institutions')
                        ->map('offset', $builder->fromArgument('offset'))
                        ->map('limit', $builder->fromArgument('limit'))
                        ->map('title', $builder->fromArgument('title'))
        );
    }

    /**
     * @param string $type
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addConnectionFields($type, ResolverRegistry $registry, ResolverBuilder $builder) {
        $registry->addFieldResolver($type, 'total',
                $builder->callback(function (QueryConnection $connection) {
                    return $connection->total();
                })
        );

        $registry->addFieldResolver($type, 'items',
                $builder->callback(function (QueryConnection $connection) {
                    return $connection->items();
                })
        );
    }

    /**
     * Add the mutations
     * @param ResolverRegistry $registry
     * @param ResolverBuilder $builder
     * @return void
     */
    private function addMutations(ResolverRegistry &$registry, ResolverBuilder &$builder): void {
        $this->includeMutations($registry, $builder, 'createProject', 'create_project');
        $this->includeMutations($registry, $builder, 'updateProject', 'update_project');
        $this->includeMutations($registry, $builder, 'deleteProject', 'delete_project');
        
        $this->includeMutations($registry, $builder, 'createPerson', 'create_person');
        $this->includeMutations($registry, $builder, 'deletePerson', 'delete_person');
        $this->includeMutations($registry, $builder, 'updatePerson', 'update_person');
        
        $this->includeMutations($registry, $builder, 'createPersonRelation', 'create_person_relation');
        $this->includeMutations($registry, $builder, 'updatePersonRelation', 'update_person_relation');
        $this->includeMutations($registry, $builder, 'deletePersonRelation', 'delete_person_relation');
        
        $this->includeMutations($registry, $builder, 'createInstitution', 'create_institution');
        $this->includeMutations($registry, $builder, 'updateInstitution', 'update_institution');
        $this->includeMutations($registry, $builder, 'deleteInstitution', 'delete_institution');
        
        $this->includeMutations($registry, $builder, 'createInstitutionRelation', 'create_institution_relation');
        
        $this->includeMutations($registry, $builder, 'createDataset', 'create_dataset');
        $this->includeMutations($registry, $builder, 'updateDataset', 'update_dataset');
        $this->includeMutations($registry, $builder, 'deleteDataset', 'delete_dataset');
        
        $this->includeMutations($registry, $builder, 'createDatasetRelation', 'create_dataset_relation');
        //$this->includeMutations($registry, $builder, 'updateDatasetRelation', 'update_dataset_relation');
        //$this->includeMutations($registry, $builder, 'deleteDatasetRelation', 'delete_dataset_relation');
        
        $this->includeMutations($registry, $builder, 'createDatasetInstance', 'create_dataset_instance');
        $this->includeMutations($registry, $builder, 'updateDatasetInstance', 'update_dataset_instance');
        $this->includeMutations($registry, $builder, 'deleteDatasetInstance', 'delete_dataset_instance');
        
        $this->includeMutations($registry, $builder, 'createDatasetInstanceRelation', 'create_dataset_instance_relation');
        
    }

}
