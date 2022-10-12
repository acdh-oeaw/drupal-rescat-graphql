<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\Schema;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistry;
use Drupal\graphql\Plugin\GraphQL\Schema\SdlSchemaPluginBase;
use Drupal\rescat_graphql\Wrappers\QueryConnection;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;
use GraphQL\Error\Error;

trait PersonSchema {

    use \Drupal\rescat_graphql\Plugin\GraphQL\Schema\IdentifierRelationSchema;

    /**
     * The Base Person Node fields
     * 
     * @param \Drupal\graphql\GraphQL\ResolverRegistry $registry
     * @param \Drupal\graphql\GraphQL\ResolverBuilder $builder
     */
    protected function addPersonFields(ResolverRegistry $registry, ResolverBuilder $builder) {
        $this->getValueFromParent($registry, $builder, 'Person', 'id', 'entity_id');
        $this->getValueByEntityNode($registry, $builder, 'Person', 'title', 'property_path', 'title.value');
        //$this->getValueByEntityNode($registry, $builder, 'Person', 'identifiers', 'property_path', 'field_identifiers.value');
        
        ///////////////// Relations //////////////////
        $registry->addFieldResolver('Person', 'identifierRelations',
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
            throw new Error('Could not resolve Paragraph type....... ' . $value->bundle());
        });

        $this->addIdentifierRelationFields($registry, $builder);
       
    }

}
