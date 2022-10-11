<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Schema;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\Plugin\GraphQL\Schema\SdlSchemaPluginBase;
use Drupal\rescat_graphql\Wrappers\QueryConnection;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;
use GraphQL\Error\Error;

trait MutationsSchema {

    /**
     * include the mutations for the data manipulation
     * @param ResolverRegistry $registry
     * @param ResolverBuilder $builder
     * @param string $type
     * @param string $producer
     */
    private function includeMutations(ResolverRegistry &$registry, ResolverBuilder &$builder, string $type, string $producer) {
        $registry->addFieldResolver('Mutation', $type,
                $builder->produce($producer)
                        ->map('data', $builder->fromArgument('data'))
        );
    }

    /**
     * Add the mutations
     * @param ResolverRegistry $registry
     * @param ResolverBuilder $builder
     * @return void
     */
    private function addMutations(ResolverRegistry &$registry, ResolverBuilder &$builder): void {
        $this->includeMutations($registry, $builder, 'createProject', 'create_project');
        $this->includeMutations($registry, $builder, 'updateProject', 'update_project');
        $this->includeMutations($registry, $builder, 'deleteProject', 'delete_project');

        $this->includeMutations($registry, $builder, 'createPerson', 'create_person');
        $this->includeMutations($registry, $builder, 'deletePerson', 'delete_person');
        $this->includeMutations($registry, $builder, 'updatePerson', 'update_person');

        $this->includeMutations($registry, $builder, 'createPersonRelation', 'create_person_relation');
        $this->includeMutations($registry, $builder, 'updatePersonRelation', 'update_person_relation');
        $this->includeMutations($registry, $builder, 'deletePersonRelation', 'delete_person_relation');

        $this->includeMutations($registry, $builder, 'createInstitution', 'create_institution');
        $this->includeMutations($registry, $builder, 'updateInstitution', 'update_institution');
        $this->includeMutations($registry, $builder, 'deleteInstitution', 'delete_institution');

        $this->includeMutations($registry, $builder, 'createInstitutionRelation', 'create_institution_relation');

        $this->includeMutations($registry, $builder, 'createDataset', 'create_dataset');
        $this->includeMutations($registry, $builder, 'updateDataset', 'update_dataset');
        $this->includeMutations($registry, $builder, 'deleteDataset', 'delete_dataset');

        $this->includeMutations($registry, $builder, 'createDatasetRelation', 'create_dataset_relation');
        //$this->includeMutations($registry, $builder, 'updateDatasetRelation', 'update_dataset_relation');
        //$this->includeMutations($registry, $builder, 'deleteDatasetRelation', 'delete_dataset_relation');

        $this->includeMutations($registry, $builder, 'createDatasetInstance', 'create_dataset_instance');
        $this->includeMutations($registry, $builder, 'updateDatasetInstance', 'update_dataset_instance');
        $this->includeMutations($registry, $builder, 'deleteDatasetInstance', 'delete_dataset_instance');

        $this->includeMutations($registry, $builder, 'createDatasetInstanceRelation', 'create_dataset_instance_relation');
    }
}