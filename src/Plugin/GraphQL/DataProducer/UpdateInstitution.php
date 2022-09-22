<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\DataProducer;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\rescat_graphql\Helper\UpdateHelper;

/**
 * Update a Institution entity.
 *
 * @DataProducer(
 *   id = "update_institution",
 *   name = @Translation("Update Institution"),
 *   description = @Translation("Update a Institution."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Institution")
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Institution data")
 *     )
 *   }
 * )
 */
class UpdateInstitution extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

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
     * Update Institution constructor.
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
     * Update an Institution.
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
        if ($this->currentUser->hasPermission("Update Institution content")) {
            $nid = $data['id'];
            $node = Node::load($nid);
            // or
            $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

            if ($node) {
                $this->helper->updateProperty($node, $data, "title", "title");
                $this->helper->updateMultiLevelProperty($node, $data, "field_identifiers", "value", "identifiers");
                $this->helper->updateBody($node, $data, "description");
                $node->save();
            }
           
            return $node;
        }
        return NULL;
    }

    

}
