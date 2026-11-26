<?php
/** Homepage "Our Team of Oncologists" cards. Markup from src/pages/home.html. */
foreach ( curie_doctors( array( 'home_only' => true ) ) as $doctor ) :
	$name = get_the_title( $doctor );
	?>
      <!-- Doctor card: <?php echo esc_html( $name ); ?> -->
      <a href="<?php echo esc_url( get_permalink( $doctor ) ); ?>" class="doctor-card group !rounded-[20px] shadow-card" data-reveal>
        <div class="relative aspect-[222/211] overflow-hidden"><div class="doctor-card__img bg-cover" style="<?php echo esc_attr( curie_photo_style( $doctor->ID, 'home' ) ); ?>" role="img" aria-label="<?php echo esc_attr( $name ); ?>"></div></div>
        <div class="flex flex-1 flex-col items-center gap-2 px-3.5 pb-[26px] pt-[22px] text-center">
          <p class="text-sm font-medium uppercase leading-[25px] tracking-heading text-moss"><?php echo esc_html( $name ); ?></p>
          <p class="flex-1 text-[15px] leading-6 tracking-body text-eucalyptus-text"><?php echo esc_html( curie_field( 'role', $doctor->ID ) ); ?></p>
          <span class="mt-1 inline-flex items-center gap-1.5 rounded-full bg-frost px-2.5 py-1 text-[13px] font-medium tracking-[0.06em] text-forest"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 21s7-6.1 7-11a7 7 0 1 0-14 0c0 4.9 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/></svg><?php echo esc_html( curie_region_label( curie_field( 'region', $doctor->ID ) ) ); ?></span>
        </div>
      </a>
<?php endforeach; ?>
