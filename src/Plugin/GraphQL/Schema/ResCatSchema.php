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
                }
            }
            throw new Error('Could not resolve content type.');
        });

        $this->addQueryFields($registry, $builder);
        $this->addArticleFields($registry, $builder);
        $this->addProjectFields($registry, $builder);

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

        $registry->addFieldResolver('Project', 'abstract',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('field_abstract.value'))
        );

        $registry->addFieldResolver('Project', 'publisher_person',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('field_publisher_person.value'))
        );

        $registry->addFieldResolver('Project', 'comments',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('field_comments_on_entry.value'))
        );

      

        $registry->addFieldResolver('Project', 'image',
                $builder->compose(
                        $builder->produce('property_path')
                                ->map('type', $builder->fromValue('entity:node'))
                                ->map('value', $builder->fromParent())
                                ->map('path', $builder->fromValue('field_image.entity')),
                        $builder->produce('image_url')
                                ->map('entity', $builder->fromParent())
                )
        );

        $registry->addFieldResolver('Project', 'coverimage',
                $builder->compose(
                        $builder->produce('property_path')
                                ->map('type', $builder->fromValue('entity:node'))
                                ->map('value', $builder->fromParent())
                                ->map('path', $builder->fromValue('field_cover_image.entity')),
                        $builder->produce('image_url')
                                ->map('entity', $builder->fromParent())
                )
        );

        $registry->addFieldResolver('Project', 'thumbnailUrl',
                $builder->compose(
                        $builder->produce('property_path')
                                ->map('type', $builder->fromValue('entity:node'))
                                ->map('value', $builder->fromParent())
                                ->map('path', $builder->fromValue('field_cover_image.entity')),
                        $builder->produce('image_derivative')
                                ->map('entity', $builder->fromParent())
                                ->map('style', $builder->fromValue('thumbnail')),
                        $builder->produce('image_style_url')
                                ->map('derivative', $builder->fromParent())
                )
        );

        $registry->addFieldResolver('Project', 'accessibility',
                $builder->produce('property_path')
                        ->map('type', $builder->fromValue('entity:node'))
                        ->map('value', $builder->fromParent())
                        ->map('path', $builder->fromValue('field_accessibility.value'))
        );

        $registry->addFieldResolver('Project', 'field_contact',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_contact'))
        );


        $registry->addFieldResolver('Project', 'contacts',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_contact'))
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
