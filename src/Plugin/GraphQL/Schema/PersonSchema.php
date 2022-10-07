<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Schema;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\Plugin\GraphQL\Schema\SdlSchemaPluginBase;
use Drupal\rescat_graphql\Wrappers\QueryConnection;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;
use GraphQL\Error\Error;

trait PersonSchema {

    protected function addPersonRelationsFields(ResolverRegistry $registry, ResolverBuilder $builder) {

        // Person relation
        $registry->addFieldResolver('PersonRelation', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('PersonRelation', 'uuid',
                $builder->produce('entity_uuid')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('PersonRelation', 'person',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_person'))
        );

         $registry->addTypeResolver('Paragraph', function ($value) {
            if ($value instanceof Paragraph) {
                switch ($value->bundle()) {
                    case 'identifier_relations': return 'IdentifierRelation';
                }
            }
            //https://github.com/drupal-graphql/graphql/pull/968
            throw new Error('Could not resolve Paragraph type. ' . $value->bundle());
        });

        $registry->addFieldResolver('IdentifierRelation', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('IdentifierRelation', 'uuid',
                $builder->produce('entity_uuid')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('IdentifierRelation', 'identifier_value',
                $builder->produce('identifier_value')
                        ->map('entity', $builder->fromParent())
        );
        
        /*
        $registry->addFieldResolver('IdentifierRelation', 'datasetInstance',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_dataset_instance_relation'))
        );

        */
        // Reading the relation of the person paragraph, pointing to a taxonomy
        $registry->addFieldResolver('PersonRelation', 'relation',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_relation'))
        );
        $registry->addFieldResolver('Relation', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );
        $registry->addFieldResolver('Relation', 'name',
                $builder->produce('entity_label')
                        ->map('entity', $builder->fromParent())
        );
    }

    /**
     * The Base Person Node fields
     * 
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addPersonFields(ResolverRegistry $registry, ResolverBuilder $builder) {
        $this->getValueFromParent($registry, $builder, 'Person', 'id', 'entity_id');
        $this->getValueByEntityNode($registry, $builder, 'Person', 'title', 'property_path', 'title.value');
        $this->getValueByEntityNode($registry, $builder, 'Person', 'identifiers', 'property_path', 'field_identifiers.value');
    }

}
