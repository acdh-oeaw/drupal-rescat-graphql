<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for Dataset mutations.
 *
 * @GraphQLInputType(
 *   id = "dataset_update_input",
 *   name = "DatasetUpdateInput",
 *   fields = {
 *     "id" = "Int",
 *     "title" = "String",
 *     "description" = {
 *        "type" = "String",
 *        "nullable" = "TRUE"
 *     },
 *     "redmineId" = {
 *        "type" = "Int",
 *        "nullable" = "TRUE"
 *     }
 *   }
 * )
 */
class DatasetUpdateInput extends InputTypePluginBase {

}
