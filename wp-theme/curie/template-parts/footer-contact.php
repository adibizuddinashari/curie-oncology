<?php
/** Footer "Contact" column: one phone per Curie clinic, then the main email. Markup from src/partials/footer.html. */
foreach ( curie_locations() as $location ) :
	$phone = curie_field( 'phone', $location->ID );
	if ( ! $phone ) {
		continue;
	}
	?>
          <div class="flex flex-col gap-1">
            <p class="text-[15px] leading-[21px] tracking-body text-body"><?php echo esc_html( curie_field( 'footer_label', $location->ID ) ?: get_the_title( $location ) ); ?></p>
            <a href="<?php echo esc_attr( curie_tel( $phone ) ); ?>" class="text-[17px] leading-[21px] tracking-body text-eucalyptus-text transition-colors duration-200 hover:text-forest"><?php echo esc_html( $phone ); ?></a>
          </div>
<?php endforeach; ?>
<?php $email = curie_setting( 'main_email', 'talk2us@curieoncology.com.my' ); ?>
          <div class="flex flex-col gap-1">
            <p class="text-[15px] leading-[21px] tracking-body text-body">Email</p>
            <a href="mailto:<?php echo esc_attr( $email ); ?>" class="text-[17px] leading-[21px] tracking-body text-eucalyptus-text transition-colors duration-200 [overflow-wrap:anywhere] hover:text-forest lg:whitespace-nowrap"><?php echo esc_html( $email ); ?></a>
          </div>
