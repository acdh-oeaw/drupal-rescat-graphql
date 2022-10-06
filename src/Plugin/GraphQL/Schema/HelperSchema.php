<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Schema;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\Plugin\GraphQL\Schema\SdlSchemaPluginBase;
use Drupal\rescat_graphql\Wrappers\QueryConnection;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;
use GraphQL\Error\Error;

trait HelperSchema {

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

}
