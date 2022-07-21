<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;

/**
 * The input type for datasetinstance mutations.
 *
 * @GraphQLInputType(
 *   id = "datasetinstance_input",
 *   name = "DatasetInstanceInput",
 *   fields = {
 *     "headline" = "String",
 *     "description" = {
 *        "type" = "String",
 *        "nullable" = "TRUE"
 *     },
 *     "harvestingStatus" = {
 *        "type" = "String",
 *        "nullable" = "TRUE"
 *     },
 *     "lastHarvestDate" = {
 *        "type" = "String",
 *        "nullable" = "TRUE"
 *     },
 *     "license" = {
 *        "type" = "String",
 *        "nullable" = "TRUE"
 *     },
 *     "locationTitle" = {
 *        "type" = "String",
 *        "nullable" = "TRUE"
 *     },
 *     "locationUri" = {
 *        "type" = "String",
 *        "nullable" = "TRUE"
 *     },
 *     "size" = {
 *        "type" = "Int",
 *        "nullable" = "TRUE"
 *     }
 *   }
 * )
 */
class DatasetInstanceInput extends InputTypePluginBase {
    
}
