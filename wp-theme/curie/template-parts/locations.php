<?php
/** "Our Locations" cards (Contact + Home). Markup from src/pages/contact-us.html. */
foreach ( curie_locations() as $i => $location ) :
	$id       = $location->ID;
	$name     = get_the_title( $location );
	$phone    = curie_field( 'phone', $id );
	$email    = curie_field( 'email', $id );
	$hours    = curie_field( 'hours', $id );
	$heading  = curie_field( 'hours_heading', $id );
	$closed   = curie_field( 'closed_note', $id );
	$position = curie_field( 'photo_position', $id ) ?: '50% 50%';
	$photo    = curie_image_url( curie_field( 'photo', $id ), 'curie-location' );
	if ( $i ) {
		echo "\n";
	}
	?>
      <!-- Location card: <?php echo esc_html( $name ); ?> -->
      <article class="flex flex-col overflow-hidden rounded-[20px] bg-white shadow-card" data-reveal>
        <div class="h-[240px] bg-cover sm:h-[300px]" style="background-image: url('<?php echo esc_url( $photo ); ?>'); background-position: <?php echo esc_attr( $position ); ?>;" role="img" aria-label="<?php echo esc_attr( curie_field( 'photo_alt', $id ) ?: $name ); ?>"></div>
        <div class="flex flex-1 flex-col gap-[22px] px-6 pb-10 pt-9 sm:px-10">
          <div class="flex flex-col gap-2">
            <p class="section-eyebrow"><?php echo esc_html( curie_field( 'eyebrow', $id ) ); ?></p>
            <h3 class="text-[24px] leading-9 tracking-[0.06em] sm:text-2xl"><?php echo esc_html( $name ); ?></h3>
          </div>
          <ul class="flex flex-1 flex-col gap-4 text-[17px] leading-7 tracking-[0.03em]">
            <li class="flex gap-3.5"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-frost"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2F5C46" stroke-width="2"><path d="M12 21s7-6.1 7-11a7 7 0 1 0-14 0c0 4.9 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/></svg></span><?php echo esc_html( curie_field( 'address', $id ) ); ?></li>
<?php if ( $phone ) : ?>
            <li class="flex items-center gap-3.5"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-frost"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2F5C46" stroke-width="2"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.9.6 2.8.7a2 2 0 0 1 1.7 2Z"/></svg></span><a href="<?php echo esc_attr( curie_tel( $phone ) ); ?>" class="hover:text-forest"><?php echo esc_html( $phone ); ?></a></li>
<?php endif; ?>
<?php if ( $email ) : ?>
            <li class="flex items-center gap-3.5"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-frost"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2F5C46" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span><a href="mailto:<?php echo esc_attr( $email ); ?>" class="min-w-0 [overflow-wrap:anywhere] hover:text-forest"><?php echo str_replace( '@', '<wbr />@', esc_html( $email ) ); ?></a></li>
<?php endif; ?>
<?php if ( $hours || $closed ) : ?>
            <li class="flex gap-3.5"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-frost"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2F5C46" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span><span class="pt-1.5"><?php
	$rows = array();
	if ( $heading ) {
		$rows[] = '<strong class="font-bold">' . esc_html( $heading ) . '</strong>';
	}
	foreach ( (array) $hours as $row ) {
		// With a heading the rows are plain (KL); without one each day label is bold (Penang).
		$days   = $heading ? esc_html( $row['days'] ) : '<strong class="font-bold">' . esc_html( $row['days'] ) . '</strong>';
		$rows[] = $days . ': ' . esc_html( $row['time'] );
	}
	if ( $closed ) {
		$rows[] = '<strong class="font-bold">' . esc_html( $closed ) . '</strong>';
	}
	echo implode( '<br />', $rows ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above
	?></span></li>
<?php endif; ?>
          </ul>
          <div class="flex flex-wrap gap-3">
<?php if ( curie_field( 'map_url', $id ) ) : ?>
            <a href="<?php echo esc_url( curie_field( 'map_url', $id ) ); ?>" target="_blank" rel="noopener" class="btn-primary">Get direction</a>
<?php endif; ?>
<?php if ( $phone ) : ?>
            <a href="<?php echo esc_attr( curie_tel( $phone ) ); ?>" class="btn-secondary" data-contact-clinic>Contact clinic</a>
<?php endif; ?>
          </div>
        </div>
      </article>
<?php endforeach; ?>
