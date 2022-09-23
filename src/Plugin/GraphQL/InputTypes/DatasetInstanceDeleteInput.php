<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for DatasetInstance delete mutations.
 *
 * @GraphQLInputType(
 *   id = "dataset_instance_delete_input",
 *   name = "DatasetInstanceDeleteInput",
 *   fields = {
 *     "id" = "Int"
 *   }
 * )
 */
class DatasetInstanceDeleteInput extends InputTypePluginBase {

}
