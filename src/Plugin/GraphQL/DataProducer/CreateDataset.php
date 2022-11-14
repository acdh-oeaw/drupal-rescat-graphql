<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\DataProducer;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Creates a new dataset instance entity.
 *
 * @DataProducer(
 *   id = "create_dataset",
 *   name = @Translation("Create Dataset"),
 *   description = @Translation("Creates a new Dataset."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Dataset")
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Dataset data")
 *     )
 *   }
 * )
 */
class CreateDataset extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

    /**
     * The current user.
     *
     * @var \Drupal\Core\Session\AccountInterface
     */
    protected $currentUser;

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
        return new static(
                $configuration,
                $plugin_id,
                $plugin_definition,
                $container->get('current_user')
        );
    }

    /**
     * CreateDataset constructor.
     *
     * @param array $configuration
     *   A configuration array containing information about the plugin instance.
     * @param string $plugin_id
     *   The plugin_id for the plugin instance.
     * @param array $plugin_definition
     *   The plugin implementation definition.
     * @param \Drupal\Core\Session\AccountInterface $current_user
     *   The current user.
     */
    public function __construct(array $configuration, string $plugin_id, array $plugin_definition, AccountInterface $current_user) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->currentUser = $current_user;
    }

    /**
     * Creates an person.
     *
     * @param array $data
     *   The title of the job.
     *
     * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface
     *   The newly created person.
     *
     * @throws \Exception
     */
    public function resolve(array $data) {
        $userRoles = $this->currentUser->getRoles();
        if (in_array('authenticated', $userRoles)) {
            $values = [
                'type' => 'dataset',
                'title' => $data['title'],
                'field_description' => $data['description'],
                'field_redmine_id' => $data['redmineId']
            ];
            $node = Node::create($values);
            $node->save();
            return $node;
        }
        throw new \Exception('You dont have enough permission to create a dataset.');
    }

}
