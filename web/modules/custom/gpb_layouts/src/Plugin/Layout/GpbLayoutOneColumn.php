<?php

namespace Drupal\gpb_layouts\Plugin\Layout;

use Drupal\gpb_layouts\GpbLayoutDefault;
/**
 * A very advanced custom layout.
 *
 * @Layout(
 *   id = "gpb_layout_one_column",
 *   label = @Translation("One Column"),
 *   category = @Translation("gpb"),
 *   template = "layouts/onecol_section/layout--onecol-section",
 *   library = "gpb_layouts/onecol_section",
 *   icon = "images/1col.png",
 *   regions = {
 *     "first" = {
 *       "label" = @Translation("Content"),
 *     }
 *   }
 * )
 */
class GpbLayoutOneColumn extends GpbLayoutDefault {

}
