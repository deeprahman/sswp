<?php
/**
 * Main Plugin Page
 *
 */
global $wpss;
?>


<h1> <?php _e("WP Securing Setup", $wpss->domain) ?> </h1>
<hr>
<h2> <?php _e("File Permission Page", $wpss->domain) ?> </h2>

<div id="my-tabs">
  <ul>
    <li><a href="#tab-1"><?php _e("File Permission Page", $wpss->domain) ?></a></li>
    <li><a href="#tab-2"><?php _e(".htacces Config", $wpss->domain) ?></a></li>
    <li><a href="#tab-3">Form 3</a></li>
  </ul>
  <div id="tab-1">
    <wp-permissions-table></wp-permissions-table>
  </div>
  <div id="tab-2">
    <?php include_once( $wpss->root . DIRECTORY_SEPARATOR . "admin/templates/protection-form.htm.php" );?>
  </div>
  <div id="tab-3">
    <form id="form-3" class="tab-form" disabled>
      <input type="text" name="product" placeholder="Product">
      <input type="number" name="quantity" placeholder="Quantity">
      <button type="submit">Submit Form 3</button>
    </form>
  </div>
</div>
