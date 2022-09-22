<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for institution mutations.
 *
 * @GraphQLInputType(
 *   id = "institution_update_input",
 *   name = "InstitutionUpdateInput",
 *   fields = {
 *     "id" = "Int",
 *     "title" = "String",
 *     "identifiers" = {
 *        "type" = "String",
 *        "nullable" = "TRUE"
 *     },
 *     "body" = {
 *        "type" = "String",
 *        "nullable" = "TRUE"
 *     }
 *   }
 * )
 */
class InstitutionUpdateInput extends InputTypePluginBase {

}
