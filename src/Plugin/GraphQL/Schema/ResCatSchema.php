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
                }
            }
            throw new Error('Could not resolve content type.');
        });

        $this->addQueryFields($registry, $builder);
        $this->addArticleFields($registry, $builder);
        $this->addProjectFields($registry, $builder);
        $this->addPersonFields($registry, $builder);

        // Re-usable connection type fields.
        $this->addConnectionFields('ArticleConnection', $registry, $builder);
        $this->addConnectionFields('ProjectConnection', $registry, $builder);
        $this->addConnectionFields('PersonConnection', $registry, $builder);

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

        $registry->addFieldResolver('Person', 'headline',
                $builder->produce('entity_label')
                        ->map('entity', $builder->fromParent())
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

    }

    /**
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addQueryFields(ResolverRegistry $registry, ResolverBuilder $builder) {
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
