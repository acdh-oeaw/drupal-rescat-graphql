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
 *     "acronyms" = "String"
 *   }
 * )
 */
class InstitutionInput extends InputTypePluginBase {

}
