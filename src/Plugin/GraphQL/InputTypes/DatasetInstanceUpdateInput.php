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
 *        "type" = "String",
 *        "nullable" = "TRUE"
 *     } 
 *      
 *   }
 * )
 */
class DatasetInstanceUpdateInput extends InputTypePluginBase {

}
