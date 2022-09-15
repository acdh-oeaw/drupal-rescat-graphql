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
 *   entity_bundle = "person_relations",
 *   secure = true,
 *   name = "createPersonRelation",
 *   type = "EntityCrudOutput!",
 *   arguments = {
 *     "id" = "String",
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

        error_log('create person paraghraph');
        error_log(print_r($args, true));
        $i = 0;
        foreach ($args['input'] as $items) {
            $paragraph[$i] = Paragraph::create(['type' => 'details']);
            $paragraph[$i]->set('field_name', $items['name']);
            $paragraph[$i]->set('parent_id', $items['parent_id']);
            $paragraph[$i]->set('parent_type', 'node');
            $paragraph[$i]->set('relations.field_person.type', 'person');
            $paragraph[$i]->set('relations.field_person.target_id', $items['target_id']);
            $paragraph[$i]->isNew();
            $paragraph[$i]->save();
            $i++;
        }

        return [
            'field_details' => $paragraph  //the full object contains all necessary details
            // 'field_details' => $paragraph->id(),
            //'target_revision_id' => $paragraph->getRevisionId(),
        ];
    }

}