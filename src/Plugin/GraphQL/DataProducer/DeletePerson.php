<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\DataProducer;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Delete a new person entity.
 *
 * @DataProducer(
 *   id = "delete_person",
 *   name = @Translation("Delete Person"),
 *   description = @Translation("Delete a person."),
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
class DeletePerson extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

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
   * CreateArticle constructor.
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
   *   The deleted person.
   *
   * @throws \Exception
   */
  public function resolve(array $data) {
    if ($this->currentUser->hasPermission("delete person content")) {
        error_log('person delete');
        $nid = $data['id'];
        $node = Node::load($nid);
        // or
        $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

        // Check if node exists with the given nid.
        if ($node) {
          $node->delete();
        }
        return $node;
    }
    return NULL;
  }

}