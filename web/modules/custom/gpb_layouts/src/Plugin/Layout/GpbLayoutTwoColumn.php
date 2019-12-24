<?php

namespace Drupal\gpb_layouts\Plugin\Layout;

use Drupal\gpb_layouts\GpbLayoutDefault;
/**
 * A very advanced custom layout.
 *
 * @Layout(
 *   id = "gpb_layout_two_column",
 *   label = @Translation("Two Column"),
 *   category = @Translation("gpb"),
 *   template = "layouts/twocol_section/twocol-section",
 *   library = "gpb_layouts/twocol_section",
 *   icon = "images/2col.png",
 *   regions = {
 *     "first" = {
 *       "label" = @Translation("First"),
 *     },
 *     "second" = {
 *       "label" = @Translation("Second"),
 *     }
 *   }
 * )
 */
class GpbLayoutTwoColumn extends GpbLayoutDefault {

}
