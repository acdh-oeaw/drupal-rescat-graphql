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
 *   id = "update_person",
 *   name = @Translation("Update Person"),
 *   description = @Translation("Update a person."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Person")
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Person data")
 *     )
 *   }
 * )
 */
class UpdatePerson extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

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
     * Create Person constructor.
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
     * Creates an person.
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
        //if ($this->currentUser->hasPermission("Update person content")) {
            $nid = $data['id'];
            $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

            if ($node && strtolower($node->bundle()) == "person") {
                $this->helper->updateProperty($node, $data, "title", "title");
                $this->helper->updateBody($node, $data, "description");
                $node->save();
            }
            return $node;
        //}
    }

}
