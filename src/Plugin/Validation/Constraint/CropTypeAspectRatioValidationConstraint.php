<?php

/**
 * @file
 * Contains \Drupal\crop\Plugin\Validation\Constraint\CropTypeAspectRatioValidationConstraint.
 */

namespace Drupal\crop\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Supports validating crop type aspect ratio.
 *
 * @Constraint(
 *   id = "CropTypeAspectRatioValidation",
 *   label = @Translation("Crop Type aspect ratio", context = "Validation")
 * )
 */
class CropTypeAspectRatioValidationConstraint extends Constraint {

  /**
   * The default violation message.
   *
   * @var string
   */
  public $message = 'Invalid format of aspect ratio. Enter a ratio in format H:W.';

}