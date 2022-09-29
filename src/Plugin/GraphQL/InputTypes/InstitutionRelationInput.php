<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for Institution mutations.
 *
 * @GraphQLInputType(
 *   id = "institution_relation_input",
 *   name = "InstitutionRelationInput",
 *   fields = {
 *     "target_id" = "Int",
 *     "parent_id" = "Int"
 *   }
 * )
 */
class InstitutionRelationInput extends InputTypePluginBase {

}
