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

    use \Drupal\rescat_graphql\Plugin\GraphQL\Schema\HelperSchema;
    use \Drupal\rescat_graphql\Plugin\GraphQL\Schema\ProjectSchema;
    use \Drupal\rescat_graphql\Plugin\GraphQL\Schema\PersonSchema;
    use \Drupal\rescat_graphql\Plugin\GraphQL\Schema\InstitutionSchema;
    use \Drupal\rescat_graphql\Plugin\GraphQL\Schema\DatasetSchema;
    use \Drupal\rescat_graphql\Plugin\GraphQL\Schema\DatasetInstanceSchema;
    use \Drupal\rescat_graphql\Plugin\GraphQL\Schema\MutationsSchema;
    
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
        
        // Re-usable connection type fields.
        $this->addConnectionFields('DatasetConnection', $registry, $builder);
        $this->addConnectionFields('DatasetInstanceConnection', $registry, $builder);
        $this->addConnectionFields('InstitutionConnection', $registry, $builder);
        $this->addConnectionFields('PersonConnection', $registry, $builder);
        $this->addConnectionFields('ProjectConnection', $registry, $builder);
        $this->addConnectionFields('TaxonomyConnection', $registry, $builder);

        return $registry;
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

   
}
