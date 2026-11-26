<?php
/**
 * Doctor profile. Replaces the five hand-built src/pages/dr-*.html pages;
 * markup copied from them, with each varying part driven by a field.
 */

the_post();
$id     = get_the_ID();
$name   = get_the_title();
$role   = curie_field( 'role', $id );
$clinic = (int) curie_field( 'clinic', $id );

// Phone for call buttons: the doctor's main clinic, else the clinic-wide number.
$phone      = $clinic ? curie_field( 'phone', $clinic ) : '';
$phone      = $phone ? $phone : curie_setting( 'main_phone', '+60 12 822 3310' );
$phone_bare = trim( preg_split( '/ext/i', $phone )[0], " .,\t" );

$clinic_address = $clinic ? ( curie_field( 'address_profile', $clinic ) ?: curie_field( 'address', $clinic ) ) : '';

curie_set_meta(
	array(
		'title'       => $name . ' - Curie Oncology',
		'description' => curie_field( 'seo_description', $id ) ?: wp_strip_all_tags( curie_field( 'summary', $id ) ),
		'nav'         => 'DOCTORS',
	)
);
get_header();

$check_icon = '<svg class="mt-1 h-4 w-4 shrink-0 text-forest" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>';
$globe_13   = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="shrink-0" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.8 3.7 5.8 3.7 9s-1.2 6.2-3.7 9c-2.5-2.8-3.7-5.8-3.7-9S9.5 5.8 12 3Z"/></svg>';
$pin_14     = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s7-6.1 7-11a7 7 0 1 0-14 0c0 4.9 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/></svg>';
?>
<!-- Breadcrumb -->
<div class="border-b border-moss/[0.08] bg-frost">
  <div class="mx-auto max-w-[1144px] px-6 py-4">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-eucalyptus-text" aria-label="Breadcrumb">
      <a href="<?php echo esc_url( curie_url( 'home' ) ); ?>" class="hover:text-forest">Home</a>
      <span aria-hidden="true">/</span>
      <a href="<?php echo esc_url( curie_url( 'doctors' ) ); ?>" class="hover:text-forest">Our Doctors</a>
      <span aria-hidden="true">/</span>
      <span class="text-moss"><?php echo esc_html( $name ); ?></span>
    </nav>
  </div>
</div>

<!-- Profile hero: doctor block (client sample layout) -->
<section class="bg-white px-6 pb-14 pt-8 lg:pb-20 lg:pt-10">
  <div class="relative mx-auto flex max-w-[1144px] flex-col lg:min-h-[640px] lg:flex-row lg:items-center">
<?php $cutout = curie_image_url( curie_field( 'photo_cutout', $id ), 'full' ); ?>
<?php if ( $cutout ) : ?>
    <div class="flex justify-center lg:absolute lg:inset-y-0 lg:right-0 lg:w-[520px] lg:items-end" data-intro-fade>
      <img src="<?php echo esc_url( $cutout ); ?>" alt="<?php echo esc_attr( $name ); ?>" class="<?php echo curie_field( 'cutout_flip', $id ) ? '-scale-x-100 ' : ''; ?>h-[340px] w-auto object-contain object-bottom sm:h-[440px] lg:h-full" />
    </div>
<?php endif; ?>
    <div class="relative z-10 flex w-full flex-col gap-5 pt-6 sm:px-11 sm:py-10 lg:w-[640px] lg:pt-10" data-intro>
      <div class="flex flex-col gap-2">
        <h1 class="text-[36px] font-medium normal-case leading-[44px] tracking-[-0.01em] text-forest sm:text-[44px] sm:leading-[50px]"><?php echo esc_html( $name ); ?></h1>
        <p class="text-[17px] leading-[25px] tracking-[0.02em]"><?php echo esc_html( curie_field( 'title_line', $id ) ?: $role ); ?></p>
      </div>
<?php if ( curie_field( 'summary', $id ) ) : ?>
      <p class="text-[17px] leading-[28px] tracking-[0.02em]"><?php echo curie_rich( curie_field( 'summary', $id ) ); // phpcs:ignore ?></p>
<?php endif; ?>
<?php
$highlights = array_values( array_filter( wp_list_pluck( (array) curie_field( 'highlights', $id ), 'text' ) ) );
if ( $highlights ) :
	// Two columns, first column takes the extra item.
	$columns = array_chunk( $highlights, (int) ceil( count( $highlights ) / 2 ) );
	echo '      <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2 sm:gap-7">';
	foreach ( $columns as $column ) {
		echo '<ul class="flex flex-col gap-2.5">';
		foreach ( $column as $item ) {
			echo '<li class="flex gap-2.5 text-sm leading-[24px] tracking-[0.02em] text-moss"><span class="mt-[9px] h-1.5 w-1.5 shrink-0 rounded-full bg-eucalyptus"></span><span>' . curie_rich( $item ) . '</span></li>';
		}
		echo '</ul>';
	}
	echo "</div>\n";
endif;
?>
      <div class="grid grid-cols-1 gap-[18px] rounded-xl border border-[#DCE7E0] p-5 sm:grid-cols-[205px_1fr] sm:gap-x-6">
        <div class="flex gap-3"><span class="flex h-[34px] w-[34px] shrink-0 items-center justify-center rounded-full bg-frost"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2F5C46" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v16"/><path d="M2 21h20"/><path d="M12 7v6M9 10h6"/><path d="M10 21v-4h4v4"/></svg></span><div><p class="text-[15px] font-bold leading-[21px] text-moss">Clinic</p><p class="text-[15px] leading-[22px]"><?php echo esc_html( $clinic_address ); ?></p></div></div>
        <div class="flex gap-3"><span class="flex h-[34px] w-[34px] shrink-0 items-center justify-center rounded-full bg-frost"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2F5C46" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span><div><p class="text-[15px] font-bold leading-[21px] text-moss">Clinic hours</p><p class="text-[15px] leading-[22px]"><?php echo implode( '<br />', array_map( 'esc_html', curie_lines( wp_strip_all_tags( curie_field( 'hours_summary', $id ) ) ) ) ); // phpcs:ignore ?></p></div></div>
        <div class="flex gap-3 sm:col-span-2"><span class="flex h-[34px] w-[34px] shrink-0 items-center justify-center rounded-full bg-frost"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2F5C46" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.8 3.7 5.8 3.7 9s-1.2 6.2-3.7 9c-2.5-2.8-3.7-5.8-3.7-9S9.5 5.8 12 3Z"/></svg></span><div><p class="text-[15px] font-bold leading-[21px] text-moss">Languages</p><p class="text-[15px] leading-[22px]"><?php echo esc_html( curie_field( 'languages', $id ) ); ?></p></div></div>
      </div>
      <div class="flex flex-wrap gap-3">
        <button type="button" class="btn-primary" <?php curie_booking_attrs( $id ); ?>>Book consultation</button>
        <a href="<?php echo esc_attr( curie_tel( $phone ) ); ?>" class="btn-secondary">Call the clinic</a>
      </div>
    </div>
  </div>
</section>

<!-- About + Schedule/Location -->
<section class="bg-white px-6 py-20">
  <div class="mx-auto grid max-w-[1144px] grid-cols-1 gap-16 lg:grid-cols-[1.5fr_1fr]">
    <div data-reveal>
      <h2 class="text-[26px] leading-[38px] sm:text-[32px] sm:leading-[42px]">About <?php echo esc_html( $name ); ?></h2>
      <div class="mt-6 space-y-5 text-lg leading-relaxed17 text-body">
<?php foreach ( curie_paragraphs( curie_field( 'about', $id ) ) as $paragraph ) : ?>
        <p><?php echo curie_rich( $paragraph ); // phpcs:ignore ?></p>
<?php endforeach; ?>
      </div>
<?php $details = array_filter( (array) curie_field( 'details', $id ) ); ?>
<?php if ( $details ) : ?>
      <div class="mt-10 grid grid-cols-1 gap-8 sm:grid-cols-2">
<?php foreach ( $details as $detail ) : ?>
<?php $lines = curie_lines( $detail['text'] ); ?>
        <div>
          <h3 class="text-[15px] font-medium uppercase tracking-label text-eucalyptus-text"><?php echo esc_html( $detail['heading'] ); ?></h3>
<?php if ( count( $lines ) > 1 ) : ?>
          <ul class="mt-3 space-y-3 leading-[28px] tracking-body text-body"><?php foreach ( $lines as $line ) { echo '<li class="flex gap-3">' . $check_icon . '<span>' . curie_rich( $line ) . '</span></li>'; } // phpcs:ignore ?></ul>
<?php else : ?>
          <p class="mt-3 leading-[30px] tracking-body text-body"><?php echo curie_rich( $lines ? $lines[0] : '' ); // phpcs:ignore ?></p>
<?php endif; ?>
        </div>
<?php endforeach; ?>
      </div>
<?php endif; ?>
    </div>

    <div class="space-y-6">
<?php $schedule = array_values( array_filter( (array) curie_field( 'schedule', $id ) ) ); ?>
<?php if ( $schedule ) : ?>
      <div class="rounded-xl2 bg-[#F8ECDC] p-8" data-reveal>
        <p class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-forest">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
          Clinic Schedule
        </p>
        <div class="mt-4 space-y-2 text-sm text-body">
<?php
	$last = count( $schedule ) - 1;
	foreach ( $schedule as $i => $row ) {
		// Rows are separated by a rule; the first has no top padding, the last no rule.
		if ( 0 === $last ) {
			$class = 'flex justify-between gap-4';
		} elseif ( 0 === $i ) {
			$class = 'flex justify-between gap-4 border-b border-seafoam/60 pb-2';
		} elseif ( $i === $last ) {
			$class = 'flex justify-between gap-4 pt-1';
		} else {
			$class = 'flex justify-between gap-4 border-b border-seafoam/60 py-2';
		}
		printf( "          <div class=\"%s\"><span>%s</span><span class=\"text-right font-medium text-moss\">%s</span></div>\n", esc_attr( $class ), esc_html( $row['day'] ), esc_html( $row['time'] ) );
	}
?>
<?php foreach ( curie_lines( curie_field( 'schedule_note', $id ) ) as $note ) : ?>
          <p class="pt-3 text-xs text-eucalyptus-text"><?php echo esc_html( $note ); ?></p>
<?php endforeach; ?>
        </div>
      </div>
<?php endif; ?>
<?php $visiting = array_values( array_filter( (array) curie_field( 'visiting_locations', $id ) ) ); ?>
      <div class="rounded-xl2 bg-frost p-8" data-reveal>
        <p class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-forest">
          <?php echo $pin_14; // phpcs:ignore ?>

          <?php echo $visiting ? 'Visiting Locations' : 'Location'; ?>

        </p>
<?php if ( ! $visiting ) : ?>
        <p class="mt-3 text-sm leading-relaxed17 text-body"><?php echo esc_html( $clinic ? curie_field( 'address', $clinic ) : '' ); ?></p>
<?php elseif ( 1 === count( $visiting ) ) : ?>
<?php $site = curie_field( 'website', $visiting[0] ); ?>
        <p class="mt-3 text-sm leading-relaxed17 text-body"><?php echo esc_html( get_the_title( $visiting[0] ) ); ?></p>
<?php if ( $site ) : ?>
        <p class="mt-5 flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-forest">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.8 3.7 5.8 3.7 9s-1.2 6.2-3.7 9c-2.5-2.8-3.7-5.8-3.7-9S9.5 5.8 12 3Z"/></svg>
          Website
        </p>
        <p class="mt-3 text-sm leading-relaxed17 text-body"><a href="<?php echo esc_url( $site ); ?>" target="_blank" rel="noopener" class="text-forest underline hover:text-moss"><?php echo esc_html( $site ); ?></a></p>
<?php endif; ?>
<?php else : ?>
<?php foreach ( $visiting as $i => $location_id ) : ?>
<?php $site = curie_field( 'website', $location_id ); ?>
        <div class="<?php echo $i ? 'mt-5 border-t border-forest/15 pt-5' : 'mt-3'; ?>">
          <p class="text-sm font-medium leading-relaxed17 text-body"><?php echo esc_html( get_the_title( $location_id ) ); ?></p>
<?php if ( $site ) : ?>
          <p class="mt-1 flex items-center gap-2 text-sm leading-relaxed17 text-forest"><?php echo $globe_13; // phpcs:ignore ?><a href="<?php echo esc_url( $site ); ?>" target="_blank" rel="noopener" class="text-forest underline hover:text-moss"><?php echo esc_html( $site ); ?></a></p>
<?php endif; ?>
        </div>
<?php endforeach; ?>
<?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- Education & Recognition -->
<section class="bg-warm px-6 py-20">
  <div class="mx-auto max-w-[1144px]">
    <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
      <div data-reveal>
        <h2 class="text-[22px] leading-[33px] sm:text-[24px]">Education &amp; Training</h2>
        <ol class="mt-8 space-y-6 border-l border-seafoam pl-6">
<?php foreach ( (array) curie_field( 'education', $id ) as $row ) : ?>
          <li class="relative">
            <span class="absolute -left-[27px] top-1 h-3 w-3 rounded-full bg-honey ring-4 ring-warm"></span>
            <p class="text-sm font-medium text-clay-text"><?php echo esc_html( $row['label'] ); ?></p>
            <p class="mt-1 text-body"><?php echo esc_html( $row['text'] ); ?></p>
          </li>
<?php endforeach; ?>
        </ol>
      </div>

      <div data-reveal>
        <h2 class="text-[22px] leading-[33px] sm:text-[24px]"><?php echo esc_html( curie_field( 'recognition_title', $id ) ?: 'Recognition' ); ?></h2>
        <ul class="mt-8 space-y-4">
<?php foreach ( (array) curie_field( 'recognition', $id ) as $row ) : ?>
          <li class="flex gap-3 text-body"><?php echo $check_icon; // phpcs:ignore ?><span><?php echo curie_rich( $row['text'] ); // phpcs:ignore ?></span></li>
<?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- Other doctors -->
<section class="bg-white px-6 pb-[104px] pt-24" aria-labelledby="other-doctors-title">
  <div class="mx-auto flex max-w-[1144px] flex-col gap-10">
    <h2 id="other-doctors-title" class="sr-only">Our other doctors</h2>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
<?php get_template_part( 'template-parts/doctors-other', null, array( 'exclude' => $id ) ); ?>
    </div>
    <div class="flex justify-end">
      <a href="<?php echo esc_url( curie_url( 'doctors' ) ); ?>" class="link-arrow">View all doctors &rarr;</a>
    </div>
  </div>
</section>

<!-- CTA Banner -->
<section class="relative bg-moss px-6 py-20">
  <div class="mx-auto max-w-[720px] text-center" data-reveal>
    <h2 class="text-3xl text-white sm:text-4xl">Consult <?php echo esc_html( $name ); ?></h2>
    <p class="mt-4 text-lg leading-relaxed17 text-frost">Contact our clinic directly to discuss your concerns or schedule a consultation.</p>
    <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
      <a href="<?php echo esc_attr( curie_tel( $phone ) ); ?>" class="inline-flex items-center gap-2 rounded-full bg-honey px-6 py-3 text-sm font-medium text-moss shadow-soft transition-transform duration-200 hover:-translate-y-0.5 active:translate-y-0">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.68 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.32 1.85.55 2.81.68A2 2 0 0 1 22 16.92Z"/></svg>
        <?php echo esc_html( $phone_bare ); ?>

      </a>
      <button type="button" <?php curie_booking_attrs( $id ); ?> class="inline-flex h-[46px] items-center gap-2 rounded-full border-[1.5px] border-white/40 px-6 text-[15px] font-medium uppercase tracking-body text-white transition-transform duration-200 hover:-translate-y-0.5 active:translate-y-0">
        Book Appointment
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </button>
    </div>
  </div>
</section>
<?php
get_footer();
