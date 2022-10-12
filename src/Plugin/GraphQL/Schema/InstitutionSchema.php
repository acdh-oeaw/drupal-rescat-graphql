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
        
        $this->getValueFromParent($registry, $builder, 'Institution', 'id', 'entity_id');
        $this->getValueFromParent($registry, $builder, 'Institution', 'title', 'entity_label');
        
        ///////////////// Relations //////////////////
        $registry->addFieldResolver('Institution', 'identifierRelations',
                $builder->produce('entity_reference_revisions')
                        ->map('entity', $builder->fromParent())
                        ->map('field', $builder->fromValue('field_identifier_relations'))
        );
    
        $registry->addTypeResolver('Paragraph', function ($value) {
            if ($value instanceof Paragraph) {
                switch ($value->bundle()) {
                    case 'identifier_relations': return 'IdentifierRelation';
                }
            }
            //https://github.com/drupal-graphql/graphql/pull/968
            throw new Error('Could not resolve Paragraph type. (institution) ' . $value->bundle());
        });

        $this->addIdentifierRelationFields($registry, $builder);
    }
}
