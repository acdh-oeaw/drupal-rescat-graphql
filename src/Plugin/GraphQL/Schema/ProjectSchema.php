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
    use \Drupal\rescat_graphql\Plugin\GraphQL\Schema\ProjectRelationSchema;

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

        $registry->addFieldResolver('ProjectRelation', 'projectRelations',
                $builder->compose(
                        $builder->produce('entity_id')
                                ->map('entity', $builder->fromParent()),
                        $builder->callback(function ($parent) {
                            error_log(print_r($parent, true));

                            
                            $paragraph = Paragraph::load(227);
                            error_log(print_r($paragraph, true));
                            return $paragraph;
                            
                            
                            
                            if (count($paragraph->get('field_person')->getValue()) > 0) {
                            if ($this->checkPerson($paragraph->get('field_person')->getValue(), $data['target_id'])) {
                                if (!$this->changeRelation($paragraph, $k, $data['relation_id'])) {
                                    throw new \Exception('Dataset relation field saving error.');
                                    }
                                }
                            }
                            
                           
                            //create new pararaph
                            $paragraph = Paragraph::create([
                            'type' => 'project_relation',
                            'parent_id' => 115,
                            'parent_type' => 'node',
                            'parent_field_name' => 'field_project_relation',
                            'field_dataset_relation' => array(
                                'target_id' => $data['target_id']
                            )
                        ]);

           
                $paragraph->isNew();
                $paragraph->save();
                            
                            $arr = array(
                                "data" => array(
                                    array(
                                        "type" => "project_relation",
                                        "id" => "51dd4d10-e720-46bb-b369-809c94a23d3b",
                                        "meta" => array(
                                            "target_revision_id" => 275,
                                            "drupal_internal__target_id" => 227
                                        )
                                    )
                                )
                            );
                            return $arr;
                            //return 227;
                            return 115;
                        })
                )
        );

        $registry->addFieldResolver('Project', 'datasetRelations',
                $builder->produce('entity_reference_revisions')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_dataset_relations'))
        );

        $registry->addFieldResolver('Project', 'personRelations',
                $builder->produce('entity_reference_revisions')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_person_relations')),
                $builder->callback(function ($parent) {
                    error_log('personban: ');
                    error_log(print_r($parent, true));
                })
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
        
        
        
        // Person relation
        $registry->addFieldResolver('ProjectRelation', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent()),
                 $builder->callback(function ($parent) {
                     return 227;
                 })
        );

        $registry->addFieldResolver('ProjectRelation', 'uuid',
                $builder->produce('entity_uuid')
                        ->map('entity', $builder->fromParent())
        );
        
        $registry->addFieldResolver('ProjectRelation', 'project',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_project'))
        );

        // Reading the relation of the person paragraph, pointing to a taxonomy
        $registry->addFieldResolver('ProjectRelation', 'relation',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_relation'))
        );
        

        $this->addPersonRelationFields($registry, $builder);
        $this->addIdentifierRelationFields($registry, $builder);
        $this->addInstitutionRelationFields($registry, $builder);
        //$this->addProjectRelationFields($registry, $builder);

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
        foreach ($paragraph_entities as $k => $v) {
            foreach ($v->get('field_project')->getValue() as $p) {
                if (isset($p['target_id'])) {
                    $data[] = $p['target_id'];
                }
            }
            //error_log(print_r($v->get('field_project')->getValue(), true));
        }
    }

    private function getDatasets($projectId): array {
        $entity_storage = \Drupal::entityTypeManager()->getStorage('paragraph');
        $paragraphs = $this->fetchParagraphs();

        if (count($paragraphs) === 0) {
            return [];
        }

        //load all the paragraphs by the ids
        $paragraph_entities = $entity_storage->loadMultiple($paragraphs);

        $projectIds = $this->getProjectIds($paragraph_entities, $projectId);

        return [];
    }

}
