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
 *     "id" = "String",
 *     "target_id" = "String",
 *     "parent_id" = "String",
 *     "relation_id" = "String"
 *   }
 * )
 */
class PersonRelationUpdateInput extends InputTypePluginBase {

}
