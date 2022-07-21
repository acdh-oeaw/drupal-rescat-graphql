<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Schema;

use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\Plugin\GraphQL\Schema\SdlSchemaPluginBase;
use Drupal\rescat_graphql\Wrappers\QueryConnection;
use Drupal\paragraphs\Entity\Paragraph;
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

        $registry->addFieldResolver('Mutation', 'createPerson',
            $builder->produce('create_person')
            ->map('data', $builder->fromArgument('data'))
        );
         
        $registry->addFieldResolver('Mutation', 'createInstitution',
            $builder->produce('create_institution')
            ->map('data', $builder->fromArgument('data'))
        ); 
        
        $registry->addFieldResolver('Mutation', 'createDatasetInstance',
            $builder->produce('create_datasetinstance')
            ->map('data', $builder->fromArgument('data'))
        ); 
        
        $registry->addFieldResolver('Mutation', 'createProject',
            $builder->produce('create_project')
            ->map('data', $builder->fromArgument('data'))
        ); 
        
        $registry->addTypeResolver('NodeInterface', function ($value) {
            if ($value instanceof NodeInterface) {
                switch ($value->bundle()) {
                    case 'dataset': return 'Dataset';
                    case 'dataset_instance': return 'DatasetInstance';
                    case 'institution': return 'Institution';                    
                    case 'person': return 'Person';
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
        $this->addProjectFields($registry, $builder);
        
        // Re-usable connection type fields.
        $this->addConnectionFields('DatasetConnection', $registry, $builder);
        $this->addConnectionFields('DatasetInstanceConnection', $registry, $builder);
        $this->addConnectionFields('InstitutionConnection', $registry, $builder);
        $this->addConnectionFields('PersonConnection', $registry, $builder);        
        $this->addConnectionFields('ProjectConnection', $registry, $builder);
        
        return $registry;
    }
    

    
    
    
     /**
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addDatasetFields(ResolverRegistry $registry, ResolverBuilder $builder) {
        $registry->addFieldResolver('Dataset', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );
        
        $registry->addFieldResolver('Dataset', 'uuid',
                $builder->produce('entity_uuid')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('Dataset', 'datasetInstance',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_dataset_instances'))
        );
        
        $registry->addFieldResolver('Dataset', 'title',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('title.value'))
        );
    }

    /**
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addDatasetInstanceFields(ResolverRegistry $registry, ResolverBuilder $builder) {
        $registry->addFieldResolver('DatasetInstance', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('DatasetInstance', 'headline',
                $builder->produce('entity_label')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('DatasetInstance', 'description',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('body.value'))
        );

        $registry->addFieldResolver('DatasetInstance', 'lastHarvestDate', $builder->compose(
            $builder->produce('property_path')
                    ->map('type', $builder->fromValue('entity:node'))
                    ->map('value', $builder->fromParent())
                    ->map('path', $builder->fromValue('field_last_harvest_date.value')),
                       
        ));

        $registry->addFieldResolver('DatasetInstance', 'harvestingStatus',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('field_harvesting_status.value'))
        );

        $registry->addFieldResolver('DatasetInstance', 'license',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('field_license.value'))
        );

        $registry->addFieldResolver('DatasetInstance', 'size',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('field_size.value'))
        );

        
        $registry->addFieldResolver('DatasetInstance', 'contributors',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_contributors'))
        );
          
        $this->createPersonTermFieldResolver($registry, $builder);
       
        $registry->addFieldResolver('DatasetInstance', 'locationUri',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('field_location.uri'))
        );
         $registry->addFieldResolver('DatasetInstance', 'locationTitle',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('field_location.title'))
        );
    }

      
     /**
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addInstitutionFields(ResolverRegistry $registry, ResolverBuilder $builder) {
        $registry->addFieldResolver('Institution', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );
        
        $registry->addFieldResolver('Institution', 'title',
                $builder->produce('entity_label')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('Institution', 'description',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('body.value'))
        );
        
        $registry->addFieldResolver('Institution', 'identifiers',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('field_identifiers.value'))
        );
    }
    
    
   
    /**
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addPersonFields(ResolverRegistry $registry, ResolverBuilder $builder) {

        $registry->addFieldResolver('Person', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );
        
        $registry->addFieldResolver('Person', 'title',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('title.value'))
        );
        
        $registry->addFieldResolver('Person', 'identifiers',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('field_identifiers.value'))
        );
    }

   
   
    
    
   

    /**
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addProjectFields(ResolverRegistry $registry, ResolverBuilder $builder) {

        $registry->addFieldResolver('Project', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('Project', 'headline',
                $builder->produce('entity_label')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('Project', 'description',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('body.value'))
        );
        
        
        $registry->addFieldResolver('Project', 'affilatedInstitutions',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_institutions'))
        );
          
        $this->createInstitutionsTermFieldResolver($registry, $builder);
        
        $registry->addFieldResolver('Project', 'contributors',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_contributors'))
        );
          
        $this->createPersonTermFieldResolver($registry, $builder);
        
        $registry->addFieldResolver('Project', 'principalInvestigators',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_principal_investigators'))
        );
          
        $this->createPersonTermFieldResolver($registry, $builder);
        
        $registry->addFieldResolver('Project', 'startDate', $builder->compose(
            $builder->produce('property_path')
                    ->map('type', $builder->fromValue('entity:node'))
                    ->map('value', $builder->fromParent())
                    ->map('path', $builder->fromValue('field_start.value'))            
        ));

        $registry->addFieldResolver('Project', 'endDate', $builder->compose(
            $builder->produce('property_path')
                    ->map('type', $builder->fromValue('entity:node'))
                    ->map('value', $builder->fromParent())
                    ->map('path', $builder->fromValue('field_end.value'))
        ));

        $registry->addFieldResolver('Project', 'redmineId',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('field_redmine_issue_id.value'))
        );

        $registry->addFieldResolver('Project', 'datasets',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_datasets'))
        );

        $this->createDatasetTermFieldResolver($registry, $builder);    
       
    }

    
    private function createDatasetTermFieldResolver(ResolverRegistry &$registry, ResolverBuilder &$builder) {
        
        $registry->addFieldResolver('DatasetTerm', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );
        
      
        $registry->addFieldResolver('DatasetTerm', 'title',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node:dataset'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('type.target_id'))
        );
    }
    
    
    private function createPersonTermFieldResolver(ResolverRegistry &$registry, ResolverBuilder &$builder) {
        
        $registry->addFieldResolver('PersonTerm', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );
        
      
        $registry->addFieldResolver('PersonTerm', 'title',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node:person'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('type.target_id'))
        );
        
        $registry->addFieldResolver('PersonTerm', 'identifiers',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node:person'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('field_identifiers.value'))
        );
    }
    
    
    private function createInstitutionsTermFieldResolver(ResolverRegistry &$registry, ResolverBuilder &$builder) {
        $registry->addFieldResolver('InstitutionsTerm', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('InstitutionsTerm', 'title',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node:institution'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('type.target_id'))
        );
        
     
        $registry->addFieldResolver('InstitutionsTerm', 'link',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:taxonomy_term'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('field_link.uri'))
        );
    }
    
    
    /**
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addQueryFields(ResolverRegistry $registry, ResolverBuilder $builder) {

     
        /*         * * PROJECT ** */
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
        );

        /*         * * DATASET ** */
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
        );

        /** * DATASET INSTANCE ***/
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
        );

        /*** Institution ***/
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

}
