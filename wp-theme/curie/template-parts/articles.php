<?php
/** Resource Centre article cards. Markup from src/pages/health-education.html. */
$articles = get_posts(
	array(
		'post_type'      => 'article',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
	)
);
foreach ( $articles as $i => $article ) :
	$id    = $article->ID;
	$title = get_the_title( $article );
	$terms = get_the_terms( $id, 'topic' );
	if ( $i ) {
		echo "\n";
	}
	?>
      <article class="overflow-hidden rounded-xl2 bg-white shadow-soft" data-reveal>
        <img src="<?php echo esc_url( curie_image_url( curie_field( 'image', $id ), 'curie-article' ) ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="aspect-[16/10] w-full object-cover"<?php $pos = curie_field( 'image_position', $id ); echo $pos ? ' style="object-position: ' . esc_attr( $pos ) . '"' : ''; ?> />
        <div class="p-8">
<?php
	foreach ( (array) ( $terms ?: array() ) as $t => $term ) :
		$colours = array( 'honey' => 'text-honey-text', 'clay' => 'text-clay-text' );
		$colour  = isset( $colours[ curie_field( 'colour', $term ) ] ) ? $colours[ curie_field( 'colour', $term ) ] : 'text-forest';
		?>
          <span class="<?php echo $t ? 'ml-2 ' : ''; ?>text-xs font-medium uppercase tracking-wide <?php echo esc_attr( $colour ); ?>"><?php echo esc_html( $term->name ); ?></span>
<?php endforeach; ?>
          <h3 class="normal-case tracking-normal mt-3 text-xl text-moss"><?php echo esc_html( $title ); ?></h3>
          <p class="mt-3 text-sm leading-relaxed17 text-body"><?php echo esc_html( curie_field( 'excerpt', $id ) ); ?></p>
          <a href="<?php echo esc_url( curie_field( 'url', $id ) ); ?>" target="_blank" rel="noopener" class="mt-5 inline-flex items-center gap-1 text-sm font-medium text-forest"><?php echo esc_html( curie_field( 'link_label', $id ) ?: 'Read on Curie Oncology SG' ); ?> <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17 17 7M8 7h9v9"/></svg></a>
        </div>
      </article>
<?php endforeach; ?>
