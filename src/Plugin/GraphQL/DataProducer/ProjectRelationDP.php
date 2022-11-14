<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\DataProducer;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Gets the ID of current user.
 *
 * @DataProducer(
 *   id = "project_relation_dp",
 *   name = @Translation("Current user"),
 *   description = @Translation("Current logged in user."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Current user")
 *   )
 * )
 */
class ProjectRelationDP extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

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
   * UserRegister constructor.
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
   * Returns current user id.
   *
   * @return int
   *   The current user id.
   */
  public function resolve() {
      return 227;
    return $paragraph = Paragraph::load(227);
  }

}