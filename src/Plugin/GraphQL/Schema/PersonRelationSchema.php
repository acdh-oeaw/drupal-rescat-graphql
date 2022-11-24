<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Schema;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\Plugin\GraphQL\Schema\SdlSchemaPluginBase;
use Drupal\rescat_graphql\Wrappers\QueryConnection;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;
use GraphQL\Error\Error;


trait PersonRelationSchema {
    
    protected function addPersonRelationFields(ResolverRegistry $registry, ResolverBuilder $builder) {
                
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
        
         $registry->addFieldResolver('PersonRelation', 'institution',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_institution'))
        );
         
        $this->getValueByEntityNode($registry, $builder, 'PersonRelation', 'start', 'property_path', 'field_start.value');
        $this->getValueByEntityNode($registry, $builder, 'PersonRelation', 'end', 'property_path', 'field_end.value');
   
    }
    
    
}
