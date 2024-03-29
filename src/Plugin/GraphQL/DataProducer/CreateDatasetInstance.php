<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\DataProducer;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\node\Entity\Node;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
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
        $userRoles = $this->currentUser->getRoles();
        
        if (in_array('authenticated', $userRoles)) {
            
            $harvestDate =  DrupalDateTime::createFromTimestamp(strtotime($data['harvestDate']));
            $harvestDate->setTimezone(new \DateTimeZone('UTC'));
        
            $values = [
                'type' => 'dataset_instance',
                'title' => $data['locationPath'],
                'body' => $data['description'],
                'field_harvest_status' => $data['harvestStatus'],
                'field_harvest_date' => $harvestDate->format('Y-m-d\TH:i:s'),
                'field_harvest_report' => $data['harvestReport'],
                'field_size' => $data['size'],
                'field_files_count' => $data['filesCount'],
                'field_notes' => $data['notes']
            ];
            $node = Node::create($values);
            $node->save();
            return $node;
        }
        throw new \Exception('You dont have enough permission to create a dataset instance.');
    }
    
    

}
