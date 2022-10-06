<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Schema;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\Plugin\GraphQL\Schema\SdlSchemaPluginBase;
use Drupal\rescat_graphql\Wrappers\QueryConnection;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;
use GraphQL\Error\Error;


trait InstitutionSchema {
    
    
    protected function addInstitutionFields(ResolverRegistry $registry, ResolverBuilder $builder) {
                
                error_log('traitben');
        $this->getValueFromParent($registry, $builder, 'Institution', 'id', 'entity_id');
        $this->getValueFromParent($registry, $builder, 'Institution', 'title', 'entity_label');
        $this->getValueByEntityNode($registry, $builder, 'Institution', 'description', 'property_path', 'body.value');
        $this->getValueByEntityNode($registry, $builder, 'Institution', 'identifiers', 'property_path', 'field_identifiers.value');
   
    }
    
    
}
