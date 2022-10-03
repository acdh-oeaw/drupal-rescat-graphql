<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\DataProducer;

use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\rescat_graphql\Wrappers\QueryConnection;
use GraphQL\Error\UserError;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DataProducer(
 *   id = "query_dataset_instances",
 *   name = @Translation("Load dataset instances"),
 *   description = @Translation("Loads a list of dataset instances."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Dataset instances connection")
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
 *     "title" = @ContextDefinition("string",
 *       label = @Translation("Title"),
 *       required = FALSE
 *     )
 *   }
 * )
 */
class QueryDatasetInstances extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

    const MAX_LIMIT = 100;

    /**
     * @var \Drupal\Core\Entity\EntityTypeManagerInterface
     */
    protected $entityManager;

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
                $container->get('entity_type.manager')
        );
    }

    /**
     * DatasetInstances constructor.
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
            $pluginId,
            $pluginDefinition,
            EntityTypeManagerInterface $entityManager
    ) {
        parent::__construct($configuration, $pluginId, $pluginDefinition);
        $this->entityManager = $entityManager;
    }

    /**
     * @param $offset
     * @param $limit
     * @param $title
     * @param \Drupal\Core\Cache\RefinableCacheableDependencyInterface $metadata
     *
     * @return \Drupal\rescat_graphql\Wrappers\QueryConnection
     * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
     * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
     */
    public function resolve($offset, $limit, $title, RefinableCacheableDependencyInterface $metadata) {
        if (!$limit > static::MAX_LIMIT) {
            throw new UserError(sprintf('Exceeded maximum query limit: %s.', static::MAX_LIMIT));
        }

        $storage = $this->entityManager->getStorage('node');
        $type = $storage->getEntityType();
        $query = $storage->getQuery()
                ->currentRevision()
                ->accessCheck();

        $query->condition($type->getKey('bundle'), 'dataset_instance');
        if ($title) {
            $query->condition($type->getKey('label'), $title);
        }
        $query->range($offset, $limit);

        $metadata->addCacheTags($type->getListCacheTags());
        $metadata->addCacheContexts($type->getListCacheContexts());

        return new QueryConnection($query);
    }

}
