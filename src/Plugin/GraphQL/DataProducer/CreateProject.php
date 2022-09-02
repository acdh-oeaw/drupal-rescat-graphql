<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\DataProducer;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Creates a new project entity.
 *
 * @DataProducer(
 *   id = "create_project",
 *   name = @Translation("Create project"),
 *   description = @Translation("Creates a new project."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Project")
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Project data")
 *     )
 *   }
 * )
 */
class CreateProject extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

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
   * CreateProject constructor.
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
   * Creates an Project.
   *
   * @param array $data
   *   The title of the job.
   *
   * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface
   *   The newly created project.
   *
   * @throws \Exception
   */
  public function resolve(array $data) {
      error_log("CREATE project");
      error_log(print_r($data, true));
    if ($this->currentUser->hasPermission("create Project content")) {
      $values = [
        'type' => 'dataset_project',
        'headline' => $data['headline'],
        'identifier' => $data['identifier'],
        'title' => $data['headline'],
        'body' => $data['description'],
        'field_start' => $data['harvestingStatus'],
        'field_end' => $data['lastHarvestDate'],
        'field_redmine_issue_id' => $data['license']
        /*'relationships.field_datasets' => array('title' => $data['datasets']['title'], 'id' =>  $data['datasets']['id']),
        'relationships.field_contributors' => array('title' => $data['contributors']['title'], 'id' =>  $data['contributors']['id']),
        'relationships.field_institutions' => array('title' => $data['institutions']['title'], 'id' =>  $data['institutions']['id']),
        'relationships.field_principal_investigators' => array('title' => $data['investigators']['title'], 'id' =>  $data['investigators']['id'])*/
      ];
      $node = Node::create($values);
      $node->save();
      return $node;
    } else {
      $response->addViolation(
        $this->t('You do not have permissions to create project.')
      );
    }
    return $response;
  }

}