<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Schema;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\Plugin\GraphQL\Schema\SdlSchemaPluginBase;
use Drupal\rescat_graphql\Wrappers\QueryConnection;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\taxonomy\Entity\Term;
use GraphQL\Error\Error;

trait ProjectSchema {

    use \Drupal\rescat_graphql\Plugin\GraphQL\Schema\IdentifierRelationSchema;
    use \Drupal\rescat_graphql\Plugin\GraphQL\Schema\PersonRelationSchema;
    use \Drupal\rescat_graphql\Plugin\GraphQL\Schema\InstitutionRelationSchema;
    
    /**
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addProjectFields(ResolverRegistry $registry, ResolverBuilder $builder) {
        
        $this->getValueFromParent($registry, $builder, 'Project', 'id', 'entity_id');
      
        $this->getValueFromParent($registry, $builder, 'Project', 'title', 'entity_label');
        $this->getValueByEntityNode($registry, $builder, 'Project', 'description', 'property_path', 'body.value');

        $this->getValueByEntityNode($registry, $builder, 'Project', 'startDate', 'property_path', 'field_start.value');
        $this->getValueByEntityNode($registry, $builder, 'Project', 'endDate', 'property_path', 'field_end.value');
        $this->getValueByEntityNode($registry, $builder, 'Project', 'redmineId', 'property_path', 'field_redmine_id.value');

        
        // we need to fetch all paragraphs with type  project_relation and check the  field_project with the target_id if equals with the actual project id
        // if yes then build up the relation
        
        
        ///////////////// Relations //////////////////
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

        $registry->addFieldResolver('Project', 'personRelations',
                $builder->produce('entity_reference_revisions')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_person_relations'))
        );
        
    
        $registry->addTypeResolver('Paragraph', function ($value) {
            if ($value instanceof Paragraph) {
                switch ($value->bundle()) {
                    case 'person_relations': return 'PersonRelation';
                    case 'identifier_relations': return 'IdentifierRelation';
                    case 'project_relation': return 'ProjectRelation';    
                    case 'institution_relations': return 'InstitutionRelation';
                    case 'dataset_relation': return 'DatasetRelation';
                }
            }
            //https://github.com/drupal-graphql/graphql/pull/968
            throw new Error('Could not resolve Paragraph type (in project) ' . $value->bundle());
        });

       
        $this->addPersonRelationFields($registry, $builder);
        $this->addIdentifierRelationFields($registry, $builder);
        $this->addInstitutionRelationFields($registry, $builder);
        
        $registry->addFieldResolver('Relation', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );
        $registry->addFieldResolver('Relation', 'name',
                $builder->produce('entity_label')
                        ->map('entity', $builder->fromParent())
        );

        ///////////////////////////////////////////////////////////////////////////////
        /*

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
         * 
         */
        
        
        //$paragraph_items = ParagraphsType::loadMultiple();
        
        //$this->getDatasets($projectId);
       

        
    }
    
    private function fetchParagraphs(): array {
        $entity_storage = \Drupal::entityTypeManager()->getStorage('paragraph');

        $query = \Drupal::entityQuery('paragraph')
        ->condition('type', "project_relation");
        try {
            return $query->execute();
        } catch (\Exception $ex) {
            return [];
        }

    }
    
    private function getProjectIds($paragraph_entities, int $projectId): array {
        $data = [];  
        foreach($paragraph_entities as $k => $v) {
                foreach($v->get('field_project')->getValue() as $p) {
                    if(isset($p['target_id'])) {
                        $data[] = $p['target_id'];
                    }
                }
                //error_log(print_r($v->get('field_project')->getValue(), true));
            
        }
    }
    
    private function getDatasets($projectId): array {
        $entity_storage = \Drupal::entityTypeManager()->getStorage('paragraph');
        $paragraphs = $this->fetchParagraphs();
        
        if(count($paragraphs) === 0) {
            return [];
        }
        
        //load all the paragraphs by the ids
        $paragraph_entities = $entity_storage->loadMultiple($paragraphs);
        
        $projectIds = $this->getProjectIds($paragraph_entities, $projectId);
        
        return [];
    }

}
