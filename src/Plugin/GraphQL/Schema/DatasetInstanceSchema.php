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

    /**
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addDatasetInstanceFields(ResolverRegistry $registry, ResolverBuilder $builder) {

        $this->getValueFromParent($registry, $builder, 'DatasetInstance', 'id', 'entity_id');
        $this->getValueFromParent($registry, $builder, 'DatasetInstance', 'headline', 'entity_label');

        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'description', 'property_path', 'body.value');
        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'lastHarvestDate', 'property_path', 'field_last_harvest_date.value');
        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'harvestingStatus', 'property_path', 'field_harvesting_status.value');
        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'license', 'property_path', 'field_license.value');
        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'size', 'property_path', 'field_size.value');

        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'locationUri', 'property_path', 'field_location.value');
        $this->getValueByEntityNode($registry, $builder, 'DatasetInstance', 'locationTitle', 'property_path', 'field_location.value');
    }

}
