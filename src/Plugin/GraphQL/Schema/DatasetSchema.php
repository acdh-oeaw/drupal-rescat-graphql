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

    use \Drupal\rescat_graphql\Plugin\GraphQL\Schema\PersonRelationSchema;
    use \Drupal\rescat_graphql\Plugin\GraphQL\Schema\ProjectRelationSchema;
    
    /**
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addDatasetFields(ResolverRegistry $registry, ResolverBuilder $builder) {

        $this->getValueFromParent($registry, $builder, 'Dataset', 'id', 'entity_id');
        $this->getValueFromParent($registry, $builder, 'Dataset', 'uuid', 'entity_uuid');

        $registry->addFieldResolver('Dataset', 'personRelations',
                $builder->produce('entity_reference_revisions')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_person_relations'))
        );
        
        $registry->addFieldResolver('Dataset', 'projectRelations',
                $builder->produce('entity_reference_revisions')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_project_relation'))
        );

         $registry->addFieldResolver('Dataset', 'identifierRelations',
                $builder->produce('entity_reference_revisions')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_identifier_relations'))
        );
        
        $registry->addTypeResolver('Paragraph', function ($value) {
            if ($value instanceof Paragraph) {
                switch ($value->bundle()) {
                    case 'person_relations': return 'PersonRelation';
                    case 'project_relation': return 'ProjectRelation';
                    case 'identifier_relations': return 'IdentifierRelation';
                }
            }
            //https://github.com/drupal-graphql/graphql/pull/968
            throw new Error('Could not resolve Paragraph type. (in dataset) ' . $value->bundle());
        });

        $this->addPersonRelationFields($registry, $builder);
        $this->addProjectRelationFields($registry, $builder);
        
        $this->getValueByEntityNode($registry, $builder, 'Dataset', 'title', 'property_path', 'title.value');
        $this->getValueByEntityNode($registry, $builder, 'Dataset', 'redmineId', 'property_path', 'field_redmine_id.value');
        $this->getValueByEntityNode($registry, $builder, 'Dataset', 'description', 'property_path', 'field_description.value');
    }

}
