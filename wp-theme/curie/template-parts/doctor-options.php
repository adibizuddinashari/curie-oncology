<?php
/** <option>s for every "Preferred doctor" select (home, contact, booking modal). */
foreach ( curie_doctors() as $doctor ) :
	$region = curie_region_label( curie_field( 'region', $doctor->ID ) );
	?>
            <option value="<?php echo esc_attr( $doctor->post_name ); ?>"><?php echo esc_html( get_the_title( $doctor ) . ( $region ? " ($region)" : '' ) ); ?></option>
<?php endforeach; ?>
