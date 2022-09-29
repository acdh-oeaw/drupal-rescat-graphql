<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for person mutations.
 *
 * @GraphQLInputType(
 *   id = "person_relation_input",
 *   name = "PersonRelationInput",
 *   fields = {
 *     "target_id" = "Int",
 *     "parent_id" = "Int",
 *     "relation_id" = "Int"
 *   }
 * )
 */
class PersonRelationInput extends InputTypePluginBase {

}
