<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Mutations;

use Drupal\graphql\Annotation\GraphQLMutation;
use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql_core\Plugin\GraphQL\Mutations\Entity\UpdateEntityBase;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Simple mutation for updating an existing Institution node.
 *
 * @GraphQLMutation(
 *   id = "update_institution",
 *   entity_type = "node",
 *   entity_bundle = "institution",
 *   secure = true,
 *   name = "updateInstitution",
 *   type = "EntityCrudOutput!",
 *   arguments = {
 *     "id" = "String",
 *     "input" = "InstitutionInput"
 *   }
 * )
 */
class UpdateInstitution extends UpdateEntityBase {

  /**
   * {@inheritdoc}
   */
  protected function extractEntityInput(
    $value,
    array $args,
    ResolveContext $context,
    ResolveInfo $info
  ) {
    return array_filter([
      'title' => $args['input']['title'],
      'identifiers' => $args['input']['identifiers'],
    ]);
  }

}