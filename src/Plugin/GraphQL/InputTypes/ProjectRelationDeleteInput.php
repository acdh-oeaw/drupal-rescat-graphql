<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for project delete mutations.
 *
 * @GraphQLInputType(
 *   id = "project_relation_delete_input",
 *   name = "ProjectRelationDeleteInput",
 *   fields = {
 *     "id" = "Int",
 *     "target_id" = "Int" 
 *   }
 * )
 */
class ProjectRelationDeleteInput extends InputTypePluginBase {

}
