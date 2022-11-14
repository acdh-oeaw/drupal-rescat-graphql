<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for person mutations.
 *
 * @GraphQLInputType(
 *   id = "person_update_input",
 *   name = "PersonUpdateInput",
 *   fields = {
 *     "id" = "Int",
 *     "title" = "String"
 *   }
 * )
 */
class PersonUpdateInput extends InputTypePluginBase {

}
