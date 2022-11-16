<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for DatasetInstance mutations.
 *
 * @GraphQLInputType(
 *   id = "dataset_instance_update_input",
 *   name = "DatasetInstanceUpdateInput",
 *   fields = {
 *     "id" = "Int",
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
class DatasetInstanceUpdateInput extends InputTypePluginBase {

}
