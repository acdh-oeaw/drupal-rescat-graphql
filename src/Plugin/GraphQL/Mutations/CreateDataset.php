<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Mutations;

use Drupal\graphql\Annotation\GraphQLMutation;
use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql_core\Plugin\GraphQL\Mutations\Entity\CreateEntityBase;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Simple mutation for creating a new Dataset node.
 *
 * @GraphQLMutation(
 *   id = "create_dataset",
 *   entity_type = "node",
 *   entity_bundle = "dataset",
 *   secure = true,
 *   name = "createDataset",
 *   type = "EntityCrudOutput!",
 *   arguments = {
 *     "input" = "DatasetInput"
 *   }
 * )
 */
class CreateDataset extends CreateEntityBase {

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
      'description' => $args['input']['description']
    ];
  }

}