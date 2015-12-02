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
   * The list of image styles using crop plugin.
   *
   * @var \Drupal\image\Entity\ImageStyle[]
   */
  protected $cropImageStyles;

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
  public function load() {
    // Load all image styles that are using crop plugin.
    $image_style_ids = $this->queryFactory->get('image_style')->condition('effects.*.id', 'crop_crop')->execute();
    $this->cropImageStyles = ImageStyle::loadMultiple($image_style_ids);

    return parent::load();
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

    // Find the used image styles for the current crop type.
    /** @var \Drupal\image\Entity\ImageStyle $image_style */
    $image_styles = [];
    $usage = [];
    foreach ($this->cropImageStyles as $image_style) {
      /** @var \Drupal\image\ImageEffectInterface $effect */
      foreach ($image_style->getEffects() as $effect) {
       if (isset($effect->getConfiguration()['data']['crop_type'])) {
         $crop_type = $effect->getConfiguration()['data']['crop_type'];
         if ($crop_type == $entity->id()) {
           // Add two image styles into the usage list.
           if (count($image_styles) < 2) {
             $usage[] = $image_style->link();
           }
           $image_styles[] = $image_style;
         }
       }
      }
    }

    $other_image_styles = array_splice($image_styles, 2);
    if ($other_image_styles) {
      $usage_message = t('@first, @second and @count more', ['@first' => $usage[0], '@second' => $usage[1], '@count' => count($other_image_styles)]);
    }
    else {
      $usage_message = implode(', ', $usage);
    }
    $row['usage']['data']['#markup'] = $usage_message;

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
