<?php

/**
 * Contains \Drupal\crop\Plugin\EntityProvider\MediaEntity.
 */

namespace Drupal\crop\Plugin\EntityProvider;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\UriItem;
use Drupal\crop\EntityProviderBase;

/**
 * Presents entity browser in an iFrame.
 *
 * @CropEntityProvider(
 *   entity_type = "media",
 *   label = @Translation("Media"),
 *   description = @Translation("Provides crop integration for media entity.")
 * )
 */
class MediaEntity extends EntityProviderBase {

  /**
   * {@inheritdoc}
   */
  public function uri(EntityInterface $entity) {
    new UriItem();
  }

}
