<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for Dataset delete mutations.
 *
 * @GraphQLInputType(
 *   id = "dataset_delete_input",
 *   name = "DatasetDeleteInput",
 *   fields = {
 *     "id" = "Int"
 *   }
 * )
 */
class DatasetDeleteInput extends InputTypePluginBase {

}
