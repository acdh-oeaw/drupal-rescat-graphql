<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Schema;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\Plugin\GraphQL\Schema\SdlSchemaPluginBase;
use Drupal\rescat_graphql\Wrappers\QueryConnection;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;
use GraphQL\Error\Error;

trait DatasetSchema {

    /**
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addDatasetFields(ResolverRegistry $registry, ResolverBuilder $builder) {

        $this->getValueFromParent($registry, $builder, 'Dataset', 'id', 'entity_id');
        $this->getValueFromParent($registry, $builder, 'Dataset', 'uuid', 'entity_uuid');

        $registry->addFieldResolver('Dataset', 'datasetInstanceRelations',
                $builder->produce('entity_reference_revisions')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_dataset_instance_relations'))
        );

        $registry->addTypeResolver('Paragraph', function ($value) {
            if ($value instanceof Paragraph) {
                switch ($value->bundle()) {
                    case 'dataset_instance_relations': return 'DatasetInstanceRelation';
                }
            }
            //https://github.com/drupal-graphql/graphql/pull/968
            throw new Error('Could not resolve Paragraph type. ' . $value->bundle());
        });

        $registry->addFieldResolver('DatasetInstanceRelation', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('DatasetInstanceRelation', 'uuid',
                $builder->produce('entity_uuid')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('DatasetInstanceRelation', 'datasetInstance',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_dataset_instance_relation'))
        );

        //$this->getValueByField($registry, $builder, 'Dataset', 'datasetInstance', 'entity_reference', 'field_dataset_instance_relations' );
        $this->getValueByEntityNode($registry, $builder, 'Dataset', 'title', 'property_path', 'title.value');
        $this->getValueByEntityNode($registry, $builder, 'Dataset', 'redmineId', 'property_path', 'field_redmineid.value');
        $this->getValueByEntityNode($registry, $builder, 'Dataset', 'projectId', 'property_path', 'field_projectid.value');
        $this->getValueByEntityNode($registry, $builder, 'Dataset', 'description', 'property_path', 'field_description.value');
    }

}
