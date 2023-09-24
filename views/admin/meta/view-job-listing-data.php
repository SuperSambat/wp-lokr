<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} ?>

<div class="wp-lokr-job-listing-data-meta-box">
    <?php foreach ( $job_listing_data_fields as $key => $field ) : ?>   
    <p class="form-field">
        <label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?>:</label>
        <?php if ( 'text' === $field['type'] ) : ?>
            <input type="text" name="<?php echo esc_attr( $key ); ?>e" id="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( get_post_meta( $post->ID, $key, true ) ); ?>" />
        <?php else : ?>
        <?php endif; ?>
    </p>
    <?php endforeach; ?>
</div>
