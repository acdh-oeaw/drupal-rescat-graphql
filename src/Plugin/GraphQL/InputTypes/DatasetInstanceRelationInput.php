<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for DatasetInstance mutations.
 *
 * @GraphQLInputType(
 *   id = "dataset_instance_relation_input",
 *   name = "DatasetInstanceRelationInput",
 *   fields = {
 *     "target_id" = "Int",
 *     "parent_id" = "Int"
 *   }
 * )
 */
class DatasetInstanceRelationInput extends InputTypePluginBase {

}
