<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Schema;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\Plugin\GraphQL\Schema\SdlSchemaPluginBase;
use Drupal\rescat_graphql\Wrappers\QueryConnection;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;
use GraphQL\Error\Error;


trait InstitutionRelationSchema {
    
    protected function addInstitutionRelationFields(ResolverRegistry $registry, ResolverBuilder $builder) {
                
        // Person relation
        $registry->addFieldResolver('InstitutionRelation', 'id',
                $builder->produce('entity_id')
                        ->map('entity', $builder->fromParent())
        );

        $registry->addFieldResolver('InstitutionRelation', 'uuid',
                $builder->produce('entity_uuid')
                        ->map('entity', $builder->fromParent())
        );
        
        $registry->addFieldResolver('InstitutionRelation', 'institution',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_institution'))
        );

        // Reading the relation of the person paragraph, pointing to a taxonomy
        $registry->addFieldResolver('InstitutionRelation', 'relation',
                $builder->produce('entity_reference')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_relation'))
        );
   
    }
    
    
}
