<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for person mutations.
 *
 * @GraphQLInputType(
 *   id = "project_update_input",
 *   name = "ProjectUpdateInput",
 *   fields = {
 *     "id" = "Int",
 *     "title" = "String",
 *     "description" = {
 *        "type" = "String",
 *        "nullable" = "TRUE"
 *     },
 *      "shortName" = {
 *        "type" = "String",
 *        "nullable" = "TRUE"
 *     }, 
 *     "endDate" = {
 *        "type" = "String",
 *        "nullable" = "TRUE"
 *     },
 *     "startDate" = {
 *        "type" = "String",
 *        "nullable" = "TRUE"
 *     }
 *   }
 * )
 */
class ProjectUpdateInput extends InputTypePluginBase {

}
