<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for person mutations.
 *
 * @GraphQLInputType(
 *   id = "person_relation_update_input",
 *   name = "PersonRelationUpdateInput",
 *   fields = {
 *     "target_id" = "Int",
 *     "parent_id" = "Int",
 *     "relation_id" = "Int"
 *   }
 * )
 */
class PersonRelationUpdateInput extends InputTypePluginBase {

}
