<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Schema;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\Plugin\GraphQL\Schema\SdlSchemaPluginBase;
use Drupal\rescat_graphql\Wrappers\QueryConnection;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;
use GraphQL\Error\Error;


trait IdentifierRelationSchema {
    
    
    protected function addIdentifierRelationFields(ResolverRegistry $registry, ResolverBuilder $builder) {
                
        // Person relation
        $registry->addFieldResolver('IdentifierRelation', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('IdentifierRelation', 'uuid',
                $builder->produce('entity_uuid')
                        ->map('entity', $builder->fromParent())
        );

        // Reading the relation of the person paragraph, pointing to a taxonomy
        $registry->addFieldResolver('IdentifierRelation', 'relation',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_relation'))
        );
        
        // Reading the relation of the person paragraph, pointing to a taxonomy
        $registry->addFieldResolver('IdentifierRelation', 'identifierService',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_identifier_service'))
        );
        
        $this->getValueByEntityNode($registry, $builder, 'IdentifierRelation', 'value', 'property_path', 'field_identifier_value.value');
        $this->getValueByEntityNode($registry, $builder, 'IdentifierRelation', 'label', 'property_path', 'field_identifier_label.value');
   
    }
    
    
}
