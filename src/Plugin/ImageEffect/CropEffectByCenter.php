<?php

/**
 * @file
 * Contains \Drupal\crop\Plugin\ImageEffect\CropEffectByCenter.
 */

namespace Drupal\crop\Plugin\ImageEffect;

use Drupal\Core\Image\ImageInterface;
use Drupal\crop\Plugin\ImageEffect\CropEffect;

/**
 * Crops an image resource.
 *
 * @ImageEffect(
 *   id = "crop_crop_center",
 *   label = @Translation("Manual crop by center"),
 *   description = @Translation("Applies manually provided crop by center to the image.")
 * )
 */
class CropEffectByCenter extends CropEffect {

  /**
   * {@inheritdoc}
   */
  public function applyEffect(ImageInterface $image) {
    if (empty($this->configuration['crop_type']) || !$this->typeStorage->load($this->configuration['crop_type'])) {
      $this->logger->error('Manual image crop failed due to misconfigured crop type on %path.', ['%path' => $image->getSource()]);
      return FALSE;
    }

    if ($crop = $this->getCrop($image)) {
      $anchor = $crop->position();
      $size = $crop->size();

      if (!$image->crop($anchor['x'], $anchor['y'], $size['width'], $size['height'])) {
        $this->logger->error('Manual image crop failed using the %toolkit toolkit on %path (%mimetype, %width x %height)', [
            '%toolkit' => $image->getToolkitId(),
            '%path' => $image->getSource(),
            '%mimetype' => $image->getMimeType(),
            '%width' => $image->getWidth(),
            '%height' => $image->getHeight(),
          ]
        );
        return FALSE;
      }
    }

    return TRUE;
  }

}
