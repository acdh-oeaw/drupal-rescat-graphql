<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for Dataset mutations.
 *
 * @GraphQLInputType(
 *   id = "dataset_relation_input",
 *   name = "DatasetRelationInput",
 *   fields = {
 *     "target_id" = "Int",
 *     "parent_id" = "Int"
 *   }
 * )
 */
class DatasetRelationInput extends InputTypePluginBase {

}
