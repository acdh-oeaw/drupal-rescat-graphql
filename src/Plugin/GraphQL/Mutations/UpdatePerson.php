<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Mutations;

use Drupal\graphql\Annotation\GraphQLMutation;
use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql_core\Plugin\GraphQL\Mutations\Entity\UpdateEntityBase;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Simple mutation for updating an existing person node.
 *
 * @GraphQLMutation(
 *   id = "update_person",
 *   entity_type = "node",
 *   entity_bundle = "person",
 *   secure = true,
 *   name = "updatePerson",
 *   type = "EntityCrudOutput!",
 *   arguments = {
 *     "id" = "String",
 *     "input" = "PersonInput"
 *   }
 * )
 */
class UpdatePerson extends UpdateEntityBase {

  /**
   * {@inheritdoc}
   */
  protected function extractEntityInput(
    $value,
    array $args,
    ResolveContext $context,
    ResolveInfo $info
  ) {
      
      error_log('itt 2');
    return array_filter([
      'title' => $args['input']['title'],
      'body' => $args['input']['body'],
    ]);
  }

}