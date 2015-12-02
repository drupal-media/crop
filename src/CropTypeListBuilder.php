<?php

/**
 * @file
 * Contains \Drupal\crop\CropTypeListBuilder.
 */

namespace Drupal\crop;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Entity\EntityTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Component\Utility\Xss;
use Drupal\image\Entity\ImageStyle;

/**
 * Defines a class to build a listing of crop type entities.
 *
 * @see \Drupal\crop\Entity\CropType
 */
class CropTypeListBuilder extends ConfigEntityListBuilder {

  /**
   * The url generator service.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * The entity query factory.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $queryFactory;

  /**
   * Constructs a CropTypeListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The url generator service.
   * @param \Drupal\Core\Entity\Query\QueryFactory $query_factory
   *   The entity query factory.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, UrlGeneratorInterface $url_generator, QueryFactory $query_factory) {
    parent::__construct($entity_type, $storage);
    $this->urlGenerator = $url_generator;
    $this->queryFactory = $query_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('url_generator'),
      $container->get('entity.query')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = [];
    $header['name'] = t('Name');
    $header['description'] = [
      'data' => t('Description'),
      'class' => [RESPONSIVE_PRIORITY_MEDIUM],
    ];
    $header['aspect_ratio'] = [
      'data' => t('Aspect Ratio'),
    ];
    $header['usage'] = $this->t('Used in');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row = [];
    $row['name'] = [
      'data' => $this->getLabel($entity),
      'class' => ['menu-label'],
    ];
    $row['description'] = Xss::filterAdmin($entity->description);
    $row['aspect_ratio'] = $entity->getAspectRatio();

    // Load all image styles where the current crop type is used.
    $usage = array();
    $image_style_ids = $this->queryFactory->get('image_style')->condition('effects.*.data.crop_type', $entity->id())->execute();
    $image_styles = ImageStyle::loadMultiple($image_style_ids);
    /** @var \Drupal\image\Entity\ImageStyle $image_style */
    foreach ($image_styles as $image_style) {
      $usage[] = \Drupal::l($image_style->label(), $image_style->urlInfo());
    }
    $row['usage']['data'] = [
      '#theme' => 'item_list',
      '#items' => $usage,
      '#context' => ['list_style' => 'comma-list'],
    ];

    return $row + parent::buildRow($entity);
  }


  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();
    $build['table']['#empty'] = t('No crop types available. <a href="@link">Add crop type</a>.', [
      '@link' => $this->urlGenerator->generateFromRoute('crop.type_add'),
    ]);
    return $build;
  }

}
