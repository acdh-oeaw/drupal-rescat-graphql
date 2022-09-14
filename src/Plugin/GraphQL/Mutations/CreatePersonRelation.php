<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Mutations;

use Drupal\graphql\Annotation\GraphQLMutation;
use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql_core\Plugin\GraphQL\Mutations\Entity\CreateEntityBase;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Simple mutation for creating a new Project node.
 *
 * @GraphQLMutation(
 *   id = "create_person_relation",
 *   entity_type = "node",
 *   entity_bundle = "person",
 *   secure = true,
 *   name = "createPersonRelation",
 *   type = "EntityCrudOutput!",
 *   arguments = {
 *     "input" = "PersonRelationInput"
 *   }
 * )
 */
class CreatePersonRelation extends CreateEntityBase {

    /**
     * {@inheritdoc}
     */
    protected function extractEntityInput(
            $value,
            array $args,
            ResolveContext $context,
            ResolveInfo $info
    ) {

        $i = 0;
        foreach ($args['input'] as $items) {
            $paragraph[$i] = Paragraph::create(['type' => 'details']);
            $paragraph[$i]->set('field_name', $items['name']);
            $paragraph[$i]->set('field_count', $items['count']);
            $paragraph[$i]->set('field_description', $items['description']);
            $paragraph[$i]->isNew();
            $paragraph[$i]->save();
            $i++;
        }

        return [
            'field_details' => $paragraph  //the full object contains all necessary details
        ];
    }

}