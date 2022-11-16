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
 *     "locationPath" = "String",
 *     "notes" = {
 *        "type" = "String",
 *        "nullable" = "TRUE"
 *     }, 
 *     "harvestingStatus" = {
 *        "type" = "String",
 *        "nullable" = "TRUE"
 *     },
 *     "harvestDate" = {
 *        "type" = "String",
 *        "nullable" = "TRUE"
 *     },
 *     "harvestReport" = {
 *        "type" = "String",
 *        "nullable" = "TRUE"
 *     },
 *     "size" = {
 *        "type" = "Int",
 *        "nullable" = "TRUE"
 *     },
 *     "filesCount" = {
 *        "type" = "Int",
 *        "nullable" = "TRUE"
 *     }
 *   }
 * )
 */
class DatasetInstanceInput extends InputTypePluginBase {
    
}
