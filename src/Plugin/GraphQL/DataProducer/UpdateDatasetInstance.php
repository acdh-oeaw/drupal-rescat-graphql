<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\DataProducer;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\rescat_graphql\Helper\UpdateHelper;

/**
 * Update a new person entity.
 *
 * @DataProducer(
 *   id = "update_dataset_instance",
 *   name = @Translation("Update DatasetInstance"),
 *   description = @Translation("Update DatasetInstance"),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("DatasetInstance")
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("DatasetInstance data")
 *     )
 *   }
 * )
 */
class UpdateDatasetInstance extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

    /**
     * The current user.
     *
     * @var \Drupal\Core\Session\AccountInterface
     */
    protected $currentUser;
    private $helper;

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
     * Create DatasetInstance constructor.
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
        $this->helper = new \Drupal\rescat_graphql\Helper\UpdateHelper();
    }

    /**
     * Creates a DatasetInstance.
     *
     * @param array $data
     *   The title of the job.
     *
     * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface
     *   The deleted person.
     *
     * @throws \Exception
     */
    public function resolve(array $data) {
        $userRoles = $this->currentUser->getRoles();
        if (in_array('authenticated', $userRoles)) {
            $nid = $data['id'];
            $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

            if ($node && strtolower($node->bundle()) == "dataset_instance") {
                $this->helper->updateProperty($node, $data, "title", "locationPath");
                $this->helper->updateBody($node, $data, "description");
                $this->helper->updateProperty($node, $data, "field_harvest_status", "harvestStatus");
                $this->helper->updateProperty($node, $data, "field_harvest_date", "harvestDate");
                $this->helper->updateProperty($node, $data, "field_harvest_report", "harvestReport");
                $this->helper->updateProperty($node, $data, "field_size", "size");
                $this->helper->updateProperty($node, $data, "field_files_count", "filesCount");
                $this->helper->updateProperty($node, $data, "field_notes", "notes");
                
                $node->save();
            }
            return $node;
        }
        throw new \Exception('You dont have enough permission to Update Dataset Instance.'); 
    }

}
