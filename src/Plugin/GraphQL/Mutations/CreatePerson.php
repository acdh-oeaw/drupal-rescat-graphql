<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Mutations;
use Drupal\graphql\Annotation\GraphQLMutation;
use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql_core\Plugin\GraphQL\Mutations\Entity\CreateEntityBase;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Simple mutation for creating a new person node.
 *
 * @GraphQLMutation(
 *   id = "create_person",
 *   entity_type = "node",
 *   entity_bundle = "person",
 *   secure = true,
 *   name = "createPerson",
 *   type = "EntityCrudOutput!",
 *   arguments = {
 *     "input" = "PersonInput"
 *   }
 * )
 */
class CreatePerson extends CreateEntityBase {

  /**
   * {@inheritdoc}
   */
  protected function extractEntityInput(
    $value,
    array $args,
    ResolveContext $context,
    ResolveInfo $info
  ) {
    return [
      'title' => $args['input']['title'],
      'body' => $args['input']['body'],
    ];
  }

}