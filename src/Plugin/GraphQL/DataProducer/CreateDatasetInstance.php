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
 *   id = "create_dataset_instance",
 *   name = @Translation("Create Dataset Instance"),
 *   description = @Translation("Creates a new Dataset Instance."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("DatasetInstance")
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Dataset Instance data")
 *     )
 *   }
 * )
 */
class CreateDatasetInstance extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

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
     * CreateDatasetInstance constructor.
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
        //if ($this->currentUser->hasPermission("create Dataset Instance content")) {
            $values = [
                'type' => 'dataset_instance',
                'headline' => $data['headline'],
                'title' => $data['headline'],
                'body' => $data['description'],
                'field_harvesting_status' => $data['harvestingStatus'],
                'field_last_harvest_date' => $data['lastHarvestDate'],
                'field_license' => $data['license'],
                'field_location' => array('title' => $data['locationTitle'], 'uri' => $data['locationUri']),
                'field_size' => $data['size'],
                'relationships.field_contributors' => array('title' => $data['contributors']['title'], 'id' => $data['contributors']['id'])
            ];
            $node = Node::create($values);
            $node->save();
            return $node;
        /*} else {
            $response->addViolation(
                    $this->t('You do not have permissions to create dataset instance.')
            );
        }
        return $response;*/
    }

}
