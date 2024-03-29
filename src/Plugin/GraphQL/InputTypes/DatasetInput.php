<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;

/**
 * The input type for dataset mutations.
 *
 * @GraphQLInputType(
 *   id = "dataset_input",
 *   name = "DatasetInput",
 *   fields = {
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
class DatasetInput extends InputTypePluginBase {
    
}
