<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Schema;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\Plugin\GraphQL\Schema\SdlSchemaPluginBase;
use Drupal\rescat_graphql\Wrappers\QueryConnection;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;
use GraphQL\Error\Error;

trait DatasetInstanceSchema {

    use \Drupal\rescat_graphql\Plugin\GraphQL\Schema\PersonRelationSchema;
    use \Drupal\rescat_graphql\Plugin\GraphQL\Schema\DatasetRelationSchema;
    
    /**
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addDatasetInstanceFields(ResolverRegistry $registry, ResolverBuilder $builder) {

        $this->getValueFromParent($registry, $builder, 'DatasetInstance', 'id', 'entity_id');
        $this->getValueFromParent($registry, $builder, 'DatasetInstance', 'locationPath', 'entity_label');
        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'description', 'property_path', 'body.value');
        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'harvestStatus', 'property_path', 'field_harvest_status.value');
        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'harvestDate', 'property_path', 'field_harvest_date.value');
        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'harvestReport', 'property_path', 'field_harvest_report.value');
        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'size', 'property_path', 'field_size.value');
        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'filesCount', 'property_path', 'field_files_count.value');
        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'notes', 'property_path', 'field_notes.value');
        
        //person relations
        
        //dataset relation
        $registry->addFieldResolver('DatasetInstance', 'personRelations',
                $builder->produce('entity_reference_revisions')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_person_relations'))
        );
        
        $registry->addFieldResolver('DatasetInstance', 'datasetRelations',
                $builder->produce('entity_reference_revisions')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_dataset_relation'))
        );

        $registry->addTypeResolver('Paragraph', function ($value) {
            if ($value instanceof Paragraph) {
                switch ($value->bundle()) {
                    case 'person_relations': return 'PersonRelation';
                    case 'dataset_relation': return 'DatasetRelation';
                }
            }
            //https://github.com/drupal-graphql/graphql/pull/968
            throw new Error('Could not resolve Paragraph type. (in datasetinstance) ' . $value->bundle());
        });

        $this->addPersonRelationFields($registry, $builder);
        $this->addDatasetRelationFields($registry, $builder);
        
        
    }

}
