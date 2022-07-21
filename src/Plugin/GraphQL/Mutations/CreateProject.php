<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Mutations;

use Drupal\graphql\Annotation\GraphQLMutation;
use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql_core\Plugin\GraphQL\Mutations\Entity\CreateEntityBase;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Simple mutation for creating a new Project node.
 *
 * @GraphQLMutation(
 *   id = "create_project",
 *   entity_type = "node",
 *   entity_bundle = "project",
 *   secure = true,
 *   name = "createProject",
 *   type = "EntityCrudOutput!",
 *   arguments = {
 *     "input" = "ProjectInput"
 *   }
 * )
 */
class CreateProject extends CreateEntityBase {

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
      'headline' => $args['input']['headline'],
      'description' => $args['input']['description'],        
      'endDate' => $args['input']['endDate'],
      'startDate' => $args['input']['startDate'],
      'redmineId' => $args['input']['redmineId']
            /*,
      'institutions' => $args['input']['institutions'],
      'investigators' => $args['input']['investigators'],
      'contributors' => $args['input']['contributors'],*/
    ];
  }

}