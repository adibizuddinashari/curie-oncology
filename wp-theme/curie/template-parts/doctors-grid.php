<?php
/** Our Doctors page cards for one region ($args['region']). Markup from src/pages/meet-our-doctors.html. */
$region = isset( $args['region'] ) ? $args['region'] : '';
foreach ( curie_doctors( array( 'region' => $region ) ) as $doctor ) :
	$name = get_the_title( $doctor );
	?>
      <!-- Doctor card: <?php echo esc_html( $name ); ?> -->
      <a href="<?php echo esc_url( get_permalink( $doctor ) ); ?>" class="doctor-card group" data-reveal>
        <div class="doctor-card__photo"><div class="doctor-card__img" style="<?php echo esc_attr( curie_photo_style( $doctor->ID, 'grid' ) ); ?>" role="img" aria-label="<?php echo esc_attr( $name ); ?>"></div></div>
        <div class="doctor-card__body">
          <p class="doctor-card__name"><?php echo esc_html( $name ); ?></p>
          <p class="doctor-card__role"><?php echo esc_html( curie_field( 'role', $doctor->ID ) ); ?></p>
        </div>
      </a>
<?php endforeach; ?>
