<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for person delete mutations.
 *
 * @GraphQLInputType(
 *   id = "person_relation_delete_input",
 *   name = "PersonRelationDeleteInput",
 *   fields = {
 *     "id" = "Int"
 *   }
 * )
 */
class PersonRelationDeleteInput extends InputTypePluginBase {

}
