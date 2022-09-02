<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Mutations;

use Drupal\graphql_core\Plugin\GraphQL\Mutations\Entity\DeleteEntityBase;

/**
 * Simple mutation for deleting an person node.
 *
 * @GraphQLMutation(
 *   id = "delete_person",
 *   entity_type = "node",
 *   entity_bundle = "person",
 *   secure = true,
 *   name = "deletePerson",
 *   type = "EntityCrudOutput!",
 *   arguments = {
 *     "id" = "String"
 *   }
 * )
 */
class DeletePerson extends DeleteEntityBase {
}