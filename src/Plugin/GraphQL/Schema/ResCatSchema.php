<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Schema;

use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\Plugin\GraphQL\Schema\SdlSchemaPluginBase;
use Drupal\rescat_graphql\Wrappers\QueryConnection;
use Drupal\paragraphs\Entity\Paragraph;

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

        $registry->addTypeResolver('NodeInterface', function ($value) {
            if ($value instanceof NodeInterface) {
                switch ($value->bundle()) {
                    case 'article': return 'Article';
                    case 'project': return 'Project';
                    case 'person': return 'Person';
                    case 'institution': return 'Institution';
                    case 'dataset': return 'Dataset';
                    case 'dataset_instance': return 'DatasetInstance';
                }
            }
            throw new Error('Could not resolve content type.');
        });

        $this->addQueryFields($registry, $builder);
        $this->addArticleFields($registry, $builder);
        $this->addProjectFields($registry, $builder);
        $this->addPersonFields($registry, $builder);
        $this->addInstitutionFields($registry, $builder);
        $this->addDatasetFields($registry, $builder);
        $this->addDatasetInstanceFields($registry, $builder);

        // Re-usable connection type fields.
        $this->addConnectionFields('ArticleConnection', $registry, $builder);
        $this->addConnectionFields('ProjectConnection', $registry, $builder);
        $this->addConnectionFields('PersonConnection', $registry, $builder);
        $this->addConnectionFields('InstitutionConnection', $registry, $builder);
        $this->addConnectionFields('DatasetConnection', $registry, $builder);
        $this->addConnectionFields('DatasetInstanceConnection', $registry, $builder);

        return $registry;
    }

    /**
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addArticleFields(ResolverRegistry $registry, ResolverBuilder $builder) {
        $registry->addFieldResolver('Article', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('Article', 'title',
                $builder->compose(
                        $builder->produce('entity_label')
                                ->map('entity', $builder->fromParent()),
                        $builder->produce('uppercase')
                                ->map('string', $builder->fromParent())
                )
        );

        $registry->addFieldResolver('Article', 'author',
                $builder->compose(
                        $builder->produce('entity_owner')
                                ->map('entity', $builder->fromParent()),
                        $builder->produce('entity_label')
                                ->map('entity', $builder->fromParent())
                )
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

        $registry->addFieldResolver('Person', 'identifierRelations',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_identifier_relations'))
        );
        
        $registry->addFieldResolver('Person', 'title',
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
    protected function addDatasetFields(ResolverRegistry $registry, ResolverBuilder $builder) {
        $registry->addFieldResolver('Dataset', 'id',
                $builder->produce('entity_id')
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
                                ->map('path', $builder->fromValue('field_harvest_date.value')),
                        $builder->callback(function ($entity) {
                            return strtotime($entity);
                        })
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

        $registry->addFieldResolver('DatasetInstance', 'personRelations',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_person_relations'))
        );

        ///// URL COVERT!!!!
        $registry->addFieldResolver('DatasetInstance', 'location',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_location'))
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

        $registry->addFieldResolver('Institution', 'identifierRelations',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_identifier_relations'))
        );
        
        $registry->addFieldResolver('Institution', 'title',
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
        
        $registry->addFieldResolver('Project', 'test',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('relationships'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('field_person_relations'))
        );

        $registry->addFieldResolver('Project', 'startDate', $builder->compose(
                        $builder->produce('property_path')
                                ->map('type', $builder->fromValue('entity:node'))
                                ->map('value', $builder->fromParent())
                                ->map('path', $builder->fromValue('field_start.value')),
                        $builder->callback(function ($entity) {
                            return strtotime($entity);
                        })
        ));

        $registry->addFieldResolver('Project', 'endDate', $builder->compose(
                        $builder->produce('property_path')
                                ->map('type', $builder->fromValue('entity:node'))
                                ->map('value', $builder->fromParent())
                                ->map('path', $builder->fromValue('field_end.value')),
                        $builder->callback(function ($entity) {
                            return strtotime($entity);
                        })
        ));

        $registry->addFieldResolver('Project', 'redmineId',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('field_redmine_issue_id.value'))
        );

        $registry->addFieldResolver('Person', 'datasets',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_datasets'))
        );

        $registry->addFieldResolver('Person', 'institutionRelations',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_institution_relations'))
        );

        $registry->addFieldResolver('Person', 'personRelations',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_person_relations'))
        );
    }

    /**
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addQueryFields(ResolverRegistry $registry, ResolverBuilder $builder) {

        /*         * * ARTICLE ** */
        $registry->addFieldResolver('Query', 'article',
                $builder->produce('entity_load')
                        ->map('type', $builder->fromValue('node'))
                        ->map('bundles', $builder->fromValue(['article']))
                        ->map('id', $builder->fromArgument('id'))
        );

        $registry->addFieldResolver('Query', 'articles',
                $builder->produce('query_articles')
                        ->map('offset', $builder->fromArgument('offset'))
                        ->map('limit', $builder->fromArgument('limit'))
        );
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
