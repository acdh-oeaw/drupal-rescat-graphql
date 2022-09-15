<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Mutations;

use Drupal\graphql\Annotation\GraphQLMutation;
use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql_core\Plugin\GraphQL\Mutations\Entity\CreateEntityBase;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Simple mutation for creating a new dataset node.
 *
 * @GraphQLMutation(
 *   id = "create_dataset_relation",
 *   entity_type = "node",
 *   entity_bundle = "dataset_relations",
 *   secure = true,
 *   name = "createDatasetRelation",
 *   type = "EntityCrudOutput!",
 *   arguments = {
 *     "id" = "String",
 *     "input" = "DatasetRelationInput"
 *   }
 * )
 */
class CreateDatasetRelation extends CreateEntityBase {

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
            //'field_details' => $paragraph  //the full object contains all necessary details
            // 'field_details' => $paragraph->id(),
            //'target_revision_id' => $paragraph->getRevisionId(),
        ];
    }

}