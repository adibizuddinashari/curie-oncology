<?php
/** "Other doctors" strip on a profile ($args['exclude'] = current doctor ID). Markup from src/pages/dr-*.html. */
$exclude = isset( $args['exclude'] ) ? (int) $args['exclude'] : 0;
foreach ( curie_doctors( array( 'exclude' => $exclude ) ) as $doctor ) :
	$name = get_the_title( $doctor );
	?>
      <!-- Other doctor: <?php echo esc_html( $name ); ?> -->
      <a href="<?php echo esc_url( get_permalink( $doctor ) ); ?>" class="doctor-card other-doctor group" data-reveal>
        <div class="other-doctor__photo"><div class="doctor-card__img" style="<?php echo esc_attr( curie_photo_style( $doctor->ID, 'other' ) ); ?>" role="img" aria-label="<?php echo esc_attr( $name ); ?>"></div></div>
        <div class="other-doctor__body">
          <p class="doctor-card__name other-doctor__name"><?php echo esc_html( $name ); ?></p>
          <p class="other-doctor__role"><?php echo esc_html( curie_field( 'role', $doctor->ID ) ); ?></p>
        </div>
      </a>
<?php endforeach; ?>
