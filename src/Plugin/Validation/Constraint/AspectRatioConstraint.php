<?php

/**
 * @file
 * Contains \Drupal\crop\Plugin\Validation\Constraint\AspectRatioConstraint.
 */

namespace Drupal\crop\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks if a value is a valid AspectRatio format.
 *
 * @Constraint(
 *   id = "AspectRatio",
 *   label = @Translation("Aspect ratio format", context = "Validation"),
 *   type = { "string"}
 * )
 */
class AspectRatioConstraint extends Constraint {

  /**
   * The default violation message.
   *
   * @var string
   */
  public $message = 'Not valid format try W:H';
}
