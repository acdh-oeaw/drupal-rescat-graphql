<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for project mutations.
 *
 * @GraphQLInputType(
 *   id = "project_input",
 *   name = "ProjectInput",
 *   fields = {
 *     "title" = "String",
 *     "body" = {
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
 *     },
 *     "redmineId" = {
 *        "type" = "Int",
 *        "nullable" = "TRUE"
 *     }
 *   }
 * )
 */
class ProjectInput extends InputTypePluginBase {

}
