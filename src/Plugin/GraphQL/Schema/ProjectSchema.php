<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Schema;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\Plugin\GraphQL\Schema\SdlSchemaPluginBase;
use Drupal\rescat_graphql\Wrappers\QueryConnection;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;
use GraphQL\Error\Error;

trait ProjectSchema {

    /**
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addProjectFields(ResolverRegistry $registry, ResolverBuilder $builder) {

        $this->getValueFromParent($registry, $builder, 'Project', 'id', 'entity_id');
        $this->getValueFromParent($registry, $builder, 'Project', 'headline', 'entity_label');
        $this->getValueByEntityNode($registry, $builder, 'Project', 'description', 'property_path', 'body.value');

        $this->getValueByEntityNode($registry, $builder, 'Project', 'startDate', 'property_path', 'field_start.value');
        $this->getValueByEntityNode($registry, $builder, 'Project', 'endDate', 'property_path', 'field_end.value');
        $this->getValueByEntityNode($registry, $builder, 'Project', 'redmineId', 'property_path', 'field_redmine_id.value');

        ///////////////// Relations //////////////////

        $registry->addFieldResolver('Project', 'personRelations',
                $builder->produce('entity_reference_revisions')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_person_relations'))
        );

        $registry->addFieldResolver('Project', 'institutionRelations',
                $builder->produce('entity_reference_revisions')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_institution_relations'))
        );

        $registry->addFieldResolver('Project', 'datasetRelations',
                $builder->produce('entity_reference_revisions')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_dataset_relations'))
        );

        $registry->addTypeResolver('Paragraph', function ($value) {
            if ($value instanceof Paragraph) {
                switch ($value->bundle()) {
                    case 'person_relations': return 'PersonRelation';
                    case 'dataset_relation': return 'DatasetRelation';
                    case 'institution_relations': return 'InstitutionRelation';
                    case 'dataset_instance_relations': return 'DatasetInstanceRelation';
                }
            }
            //https://github.com/drupal-graphql/graphql/pull/968
            throw new Error('Could not resolve Paragraph type. ' . $value->bundle());
        });

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

        // Institution Relation
        $registry->addFieldResolver('InstitutionRelation', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('InstitutionRelation', 'uuid',
                $builder->produce('entity_uuid')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('InstitutionRelation', 'institution',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_institution'))
        );

        //$this->createInstitutionsTermFieldResolver($registry, $builder);
        $registry->addFieldResolver('DatasetRelation', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('DatasetRelation', 'uuid',
                $builder->produce('entity_uuid')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('DatasetRelation', 'dataset',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_dataset_relation'))
        );
    }

}
