<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;


/**
 * The input type for identifier service mutations.
 *
 * @GraphQLInputType(
 *   id = "identifier_service_input",
 *   name = "IdentifierServiceInput",
 *   fields = {
 *     "name" = "String"
 *   }
 * )
 */
class IdentifierServiceInput extends InputTypePluginBase {

}
