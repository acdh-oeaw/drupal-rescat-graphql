<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Mutations;

use Drupal\graphql_core\Plugin\GraphQL\Mutations\Entity\DeleteEntityBase;

/**
 * Simple mutation for deleting an institution node.
 *
 * @GraphQLMutation(
 *   id = "delete_institution",
 *   entity_type = "node",
 *   entity_bundle = "institution",
 *   secure = true,
 *   name = "deleteInstitution",
 *   type = "EntityCrudOutput!",
 *   arguments = {
 *     "id" = "String"
 *   }
 * )
 */
class DeleteInstitution extends DeleteEntityBase {
}