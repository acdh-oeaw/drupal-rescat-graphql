<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for project mutations.
 *
 * @GraphQLInputType(
 *   id = "project_relation_input",
 *   name = "ProjectRelationInput",
 *   fields = {
 *     "target_id" = "Int",
 *     "parent_id" = "Int",
 *     "relation_target_id" = "Int"
 *   }
 * )
 */
class ProjectRelationInput extends InputTypePluginBase {

}
