<?php
/**
 * Query Report Admin Page
 *
 * Render the shortcode on an admin back end page
 *
 * @package	 ABS
 * @since    2.0.0
 */
?>

<div class="wrap">
    <h1>Woocommerce Query Report</h1>

    <p>
        If you want to display this on a page on the front end, you can use the shortcode <code>[wqr]</code>.
    </p>

    <?php
    do_shortcode( '[wqr]' );
    ?>

</div>