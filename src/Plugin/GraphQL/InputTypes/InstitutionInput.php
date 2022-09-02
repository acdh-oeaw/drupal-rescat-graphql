<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for institution mutations.
 *
 * @GraphQLInputType(
 *   id = "institution_input",
 *   name = "InstitutionInput",
 *   fields = {
 *     "title" = "String",
 *     "identifiers" = {
 *        "type" = "String",
 *        "nullable" = "TRUE"
 *     }
 *   }
 * )
 */
class InstitutionInput extends InputTypePluginBase {

}
