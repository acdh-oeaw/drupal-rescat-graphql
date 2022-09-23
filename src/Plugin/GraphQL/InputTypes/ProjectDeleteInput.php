<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for Project delete mutations.
 *
 * @GraphQLInputType(
 *   id = "project_delete_input",
 *   name = "ProjectDeleteInput",
 *   fields = {
 *     "id" = "Int"
 *   }
 * )
 */
class ProjectDeleteInput extends InputTypePluginBase {

}
