<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for person mutations.
 *
 * @GraphQLInputType(
 *   id = "person_input",
 *   name = "PersonInput",
 *   fields = {
 *     "nid" = "Int",
 *     "title" = "String"
 *   }
 * )
 */
class PersonInput extends InputTypePluginBase {

}
