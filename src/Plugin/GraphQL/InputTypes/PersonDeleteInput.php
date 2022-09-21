<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for person delete mutations.
 *
 * @GraphQLInputType(
 *   id = "person_delete_input",
 *   name = "PersonDeleteInput",
 *   fields = {
 *     "id" = "Int"
 *   }
 * )
 */
class PersonDeleteInput extends InputTypePluginBase {

}
