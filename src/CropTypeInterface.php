<?php

/**
 * @file
 * Contains \Drupal\crop\CropTypeInterface.
 */

namespace Drupal\crop;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a crop type entity.
 */
interface CropTypeInterface extends ConfigEntityInterface {
  /**
   * Get aspect ratio of this crop type.
   *
   * @return string|null
   *   The aspect ratio of this crop type.
   */
  public function getAspectRatio();
}
