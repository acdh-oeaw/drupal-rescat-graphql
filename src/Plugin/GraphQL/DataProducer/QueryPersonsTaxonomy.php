<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\DataProducer;

use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\TranslatableInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\GraphQL\Buffers\EntityBuffer;
use Drupal\graphql\GraphQL\Execution\FieldContext;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\rescat_graphql\Wrappers\QueryConnection;
use GraphQL\Error\UserError;
use Drupal\taxonomy\Entity\Term;      
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DataProducer(
 *   id = "query_personstaxonomy",
 *   name = @Translation("Load persons taxonomy"),
 *   description = @Translation("Loads a list of persons taxonomy."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Person taxonomy")
 *   ),
 *   consumes = {
 *     "offset" = @ContextDefinition("integer",
 *       label = @Translation("Offset"),
 *       required = FALSE
 *     ),
 *     "limit" = @ContextDefinition("integer",
 *       label = @Translation("Limit"),
 *       required = FALSE
 *     ),
 *      "name" = @ContextDefinition("string",
 *       label = @Translation("name"),
 *       required = FALSE
 *     )
 *   }
 * )
 */
class QueryPersonsTaxonomy extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

  const MAX_LIMIT = 100;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityManager;

  /**
   * The entity buffer service.
   *
   * @var \Drupal\graphql\GraphQL\Buffers\EntityBuffer
   */
  protected $entityBuffer;

  /**
   * {@inheritdoc}
   *
   * @codeCoverageIgnore
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('graphql.buffer.entity')
    );
  }

  /**
   * Persons constructor.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $pluginId
   *   The plugin id.
   * @param mixed $pluginDefinition
   *   The plugin definition.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityManager
   *
   * @codeCoverageIgnore
   */
  public function __construct(          
    array $configuration,
    string $pluginId,
    array $pluginDefinition,
    EntityTypeManagerInterface $entityTypeManager,
    EntityBuffer $entityBuffer
  ) {
    parent::__construct($configuration, $pluginId, $pluginDefinition);
    $this->entityTypeManager = $entityTypeManager;
    $this->entityBuffer = $entityBuffer;
  }

  /**
   * @param $offset
   * @param $limit
   * @param $name
   * @param \Drupal\Core\Cache\RefinableCacheableDependencyInterface $metadata
   *
   * @return \Drupal\rescat_graphql\Wrappers\QueryConnection
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function resolve($offset, $limit, $name, RefinableCacheableDependencyInterface $metadata) {
    if (!$limit > static::MAX_LIMIT) {
      throw new UserError(sprintf('Exceeded maximum query limit: %s.', static::MAX_LIMIT));
    }
    
    error_log(print_r($name, true));
    $storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $type = $storage->getEntityType();
    $query = $storage->getQuery()
      ->currentRevision()
      ->accessCheck();

    $query->condition($type->getKey('bundle'), 'entity_relations');
    if($name) {
        $query->condition($type->getKey('label'), $name);
    }
    $query->range($offset, $limit);

    $metadata->addCacheTags($type->getListCacheTags());
    $metadata->addCacheContexts($type->getListCacheContexts());
    
    return new QueryConnection($query);
    
    /*
    
   $vid = 'vocabulary_name';
    $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('entity_relations');
   
    $arr = [];
    foreach($terms as $k => $v) {
        $arr[$k]['id'] = $v->tid;
        $arr[$k]['name'] = $v->name;
        $arr[$k]['depth'] = $v->depth;
    }
   error_log(print_r($terms, true));
    
    //////////////////////
   
    return new QueryConnection($arr);
    
    
    
    //solution 2
    $query = \Drupal::entityQuery('taxonomy_term');
    error_log('eeee');
    $query->condition('vid', 'entity_relations');
    $query->sort('weight');
    //we can search by title also
    if($title) {
        $query->condition($type->getKey('label'), $title);
    }
    
    $query->range($offset, $limit);

    
    return new QueryConnection($query);
     * 
     */
  }
}
