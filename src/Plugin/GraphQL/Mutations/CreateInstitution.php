<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Mutations;

use Drupal\graphql\Annotation\GraphQLMutation;
use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql_core\Plugin\GraphQL\Mutations\Entity\CreateEntityBase;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Simple mutation for creating a new Institution node.
 *
 * @GraphQLMutation(
 *   id = "create_institution",
 *   entity_type = "node",
 *   entity_bundle = "institution",
 *   secure = true,
 *   name = "createInstitution",
 *   type = "EntityCrudOutput!",
 *   arguments = {
 *     "input" = "InstitutionInput"
 *   }
 * )
 */
class CreateInstitution extends CreateEntityBase {

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
      'identifiers' => $args['input']['identifiers']
    ];
  }

}