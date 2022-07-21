<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Mutations;

use Drupal\graphql\Annotation\GraphQLMutation;
use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql_core\Plugin\GraphQL\Mutations\Entity\CreateEntityBase;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Simple mutation for creating a new DatasetInstance node.
 *
 * @GraphQLMutation(
 *   id = "create_datasetinstance",
 *   entity_type = "node",
 *   entity_bundle = "dataset_instance",
 *   secure = true,
 *   name = "createDatasetInstance",
 *   type = "EntityCrudOutput!",
 *   arguments = {
 *     "input" = "DatasetInstanceInput"
 *   }
 * )
 */
class CreateDatasetInstance extends CreateEntityBase {

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
      'harvestingStatus' => $args['input']['harvestingStatus'],
      'lastHarvestDate' => $args['input']['lastHarvestDate'],
      'license' => $args['input']['license'],
      'locationTitle' => $args['input']['locationTitle'],
      'locationUri' => $args['input']['locationUri'],
      'size' => $args['input']['size'],
      'contributors' => $args['input']['contributors'],
    ];
  }

}