<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for institution mutations.
 *
 * @GraphQLInputType(
 *   id = "institution_delete_input",
 *   name = "InstitutionDeleteInput",
 *   fields = {
 *     "id" = "Int"
 *   }
 * )
 */
class InstitutionDeleteInput extends InputTypePluginBase {

}
