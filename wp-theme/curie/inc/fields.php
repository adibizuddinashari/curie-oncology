<?php
/**
 * Edit forms for Doctors, Locations, Articles, Topics and Clinic Settings.
 *
 * Registered in code (not via the ACF UI) so every install has identical
 * fields and changes go through version control. Requires the free
 * Secure Custom Fields plugin (WordPress.org), which includes repeaters and
 * the options page. Free ACF lacks both; paid ACF Pro also works.
 */

/* Warn admins if the active fields plugin can't do repeaters (e.g. free ACF). */
add_action(
	'admin_notices',
	function () {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$ok = function_exists( 'acf_get_field_type' ) && acf_get_field_type( 'repeater' ) && function_exists( 'acf_add_options_page' );
		if ( ! $ok ) {
			echo '<div class="notice notice-error"><p><strong>Curie Oncology theme:</strong> install and activate the free <em>Secure Custom Fields</em> plugin (Plugins &rarr; Add New). Without it, doctor schedules, hours and lists cannot be edited.</p></div>';
		}
	}
);

defined( 'ABSPATH' ) || exit;

/** Short-hand field builder: keys are derived from group + name so they stay stable. */
function curie_f( $group, $name, $type, $label, array $extra = array() ) {
	$field = array_merge(
		array(
			'key'   => "field_curie_{$group}_{$name}",
			'name'  => $name,
			'type'  => $type,
			'label' => $label,
		),
		$extra
	);
	if ( isset( $field['sub_fields'] ) ) {
		foreach ( $field['sub_fields'] as &$sub ) {
			$sub['key'] = "field_curie_{$group}_{$name}_{$sub['name']}";
		}
	}
	return $field;
}

function curie_tab( $group, $label ) {
	return array(
		'key'       => 'field_curie_' . $group . '_tab_' . sanitize_key( $label ),
		'label'     => $label,
		'type'      => 'tab',
		'placement' => 'top',
	);
}

add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		if ( function_exists( 'acf_add_options_page' ) ) {
			acf_add_options_page(
				array(
					'page_title' => 'Clinic Settings',
					'menu_title' => 'Clinic Settings',
					'menu_slug'  => 'clinic-settings',
					'capability' => 'edit_posts',
					'icon_url'   => 'dashicons-admin-generic',
					'position'   => 8,
				)
			);
		}

		/* ------------------------------------------------------------------ Doctor */
		$d = 'doctor';
		acf_add_local_field_group(
			array(
				'key'                   => 'group_curie_doctor',
				'title'                 => 'Doctor profile',
				'location'              => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'doctor' ) ) ),
				'position'              => 'acf_after_title',
				'style'                 => 'seamless',
				'hide_on_screen'        => array( 'the_content', 'excerpt', 'discussion', 'comments', 'author', 'format', 'featured_image', 'categories', 'tags', 'send-trackbacks' ),
				'fields'                => array(
					curie_tab( $d, 'Basics' ),
					curie_f( $d, 'role', 'text', 'Job title', array( 'required' => 1, 'instructions' => 'Shown under the name everywhere, e.g. "Consultant Clinical Oncologist".' ) ),
					curie_f( $d, 'title_line', 'text', 'Title line on profile', array( 'instructions' => 'The full line under the name at the top of the profile, e.g. "Consultant Clinical Oncologist (Resident) · MBBS (IMU), MCO (UM)". Leave empty to use the Job title.' ) ),
					curie_f(
						$d,
						'region',
						'select',
						'Region',
						array(
							'required'     => 1,
							'choices'      => array( 'klang-valley' => 'Klang Valley', 'penang' => 'Penang' ),
							'instructions' => 'Decides which group the doctor appears in on the Our Doctors page and in booking forms.',
						)
					),
					curie_f( $d, 'show_on_home', 'true_false', 'Show on the homepage', array( 'ui' => 1, 'default_value' => 1, 'instructions' => 'Include this doctor in "Our Team of Oncologists" on the homepage.' ) ),
					curie_f( $d, 'summary', 'textarea', 'Introduction', array( 'rows' => 3, 'new_lines' => '', 'instructions' => 'One short paragraph at the top of the profile.' ) ),
					curie_f(
						$d,
						'highlights',
						'repeater',
						'Highlights',
						array(
							'instructions' => 'Short bullet points next to the introduction (best with 2–6).',
							'layout'       => 'table',
							'button_label' => 'Add highlight',
							'sub_fields'   => array( array( 'name' => 'text', 'label' => 'Highlight', 'type' => 'text' ) ),
						)
					),

					curie_tab( $d, 'Clinic & schedule' ),
					curie_f(
						$d,
						'clinic',
						'post_object',
						'Main clinic',
						array(
							'post_type'     => array( 'location' ),
							'return_format' => 'id',
							'instructions'  => 'The clinic address shown on the profile comes from this location, so updating the location updates every profile.',
						)
					),
					curie_f( $d, 'hours_summary', 'textarea', 'Clinic hours (short)', array( 'rows' => 3, 'new_lines' => 'br', 'instructions' => 'Shown in the box at the top of the profile. One line per row, e.g. "Mon, Tue: 8.30am – 1pm".' ) ),
					curie_f( $d, 'languages', 'text', 'Languages', array( 'instructions' => 'e.g. "English, Bahasa Malaysia, Cantonese".' ) ),
					curie_f(
						$d,
						'schedule',
						'repeater',
						'Clinic schedule',
						array(
							'instructions' => 'Full weekly schedule shown in the "Clinic Schedule" card. Add one row per day the doctor sees patients.',
							'layout'       => 'table',
							'button_label' => 'Add day',
							'sub_fields'   => array(
								array( 'name' => 'day', 'label' => 'Day', 'type' => 'text', 'placeholder' => 'Monday' ),
								array( 'name' => 'time', 'label' => 'Hours', 'type' => 'text', 'placeholder' => '8:30AM–1:00PM, 2:00PM–5:30PM' ),
							),
						)
					),
					curie_f( $d, 'schedule_note', 'textarea', 'Schedule notes', array( 'rows' => 2, 'new_lines' => '', 'default_value' => 'Closed on Sundays and public holidays.', 'instructions' => 'Small print under the schedule. One note per line.' ) ),
					curie_f(
						$d,
						'visiting_locations',
						'relationship',
						'Visiting locations',
						array(
							'post_type'     => array( 'location' ),
							'filters'       => array( 'search' ),
							'return_format' => 'id',
							'instructions'  => 'Other hospitals or centres where the doctor also consults (add them under Locations first). Leave empty to show the main clinic address instead.',
						)
					),

					curie_tab( $d, 'Profile' ),
					curie_f( $d, 'about', 'textarea', 'About', array( 'rows' => 10, 'new_lines' => '', 'instructions' => 'The "About Dr …" section. Leave an empty line between paragraphs. For italics (e.g. a book title) wrap words in &lt;em&gt;…&lt;/em&gt;.' ) ),
					curie_f(
						$d,
						'details',
						'repeater',
						'Specialties',
						array(
							'instructions' => 'Small headed sections under "About", e.g. "Cancer types managed", "Clinical interests". Best with 2.',
							'layout'       => 'block',
							'button_label' => 'Add section',
							'sub_fields'   => array(
								array( 'name' => 'heading', 'label' => 'Heading', 'type' => 'text' ),
								array( 'name' => 'text', 'label' => 'Text', 'type' => 'textarea', 'rows' => 3, 'new_lines' => '', 'instructions' => 'Write a sentence, OR put one item per line to show a ticked list.' ),
							),
						)
					),
					curie_f(
						$d,
						'education',
						'repeater',
						'Education & training',
						array(
							'layout'       => 'table',
							'button_label' => 'Add entry',
							'sub_fields'   => array(
								array( 'name' => 'label', 'label' => 'Year', 'type' => 'text', 'placeholder' => '1997', 'wrapper' => array( 'width' => 20 ) ),
								array( 'name' => 'text', 'label' => 'Qualification', 'type' => 'text' ),
							),
						)
					),
					curie_f( $d, 'recognition_title', 'text', 'Recognition heading', array( 'default_value' => 'Recognition', 'instructions' => 'e.g. "Recognition", "Recognition & Memberships", "Leadership & Teaching".' ) ),
					curie_f(
						$d,
						'recognition',
						'repeater',
						'Recognition items',
						array(
							'layout'       => 'table',
							'button_label' => 'Add item',
							'sub_fields'   => array( array( 'name' => 'text', 'label' => 'Item', 'type' => 'text' ) ),
						)
					),

					curie_tab( $d, 'Photos' ),
					curie_f( $d, 'photo_portrait', 'image', 'Portrait (homepage & booking)', array( 'return_format' => 'array', 'preview_size' => 'medium', 'required' => 1, 'instructions' => 'Head-and-shoulders photo, at least 600px wide.' ) ),
					curie_f( $d, 'photo_card', 'image', 'Card photo (Our Doctors page)', array( 'return_format' => 'array', 'preview_size' => 'medium', 'instructions' => 'Portrait photo, at least 640px wide, face in the upper third. Falls back to the portrait.' ) ),
					curie_f( $d, 'photo_cutout', 'image', 'Profile cut-out', array( 'return_format' => 'array', 'preview_size' => 'medium', 'mime_types' => 'png,webp', 'instructions' => 'Transparent PNG of the doctor (background removed) for the top of the profile page.' ) ),
					curie_f( $d, 'cutout_flip', 'true_false', 'Mirror the cut-out', array( 'ui' => 1, 'instructions' => 'Turn on if the doctor faces right in the photo, so they face the text.' ) ),
					curie_f( $d, 'photo_focus_x', 'range', 'Photo focus, left ↔ right', array( 'min' => 0, 'max' => 100, 'default_value' => 50, 'append' => '%', 'instructions' => 'Move the crop so the face is centred on the cards.' ) ),
					curie_f( $d, 'photo_focus_y', 'range', 'Photo focus, top ↕ bottom', array( 'min' => 0, 'max' => 100, 'default_value' => 20, 'append' => '%' ) ),
					curie_f( $d, 'photo_zoom', 'range', 'Photo zoom', array( 'min' => 100, 'max' => 250, 'default_value' => 100, 'append' => '%' ) ),

					curie_tab( $d, 'Advanced' ),
					curie_f( $d, 'crop_home', 'text', 'Developer crop: homepage card', array( 'instructions' => 'Leave empty unless set by the developer. When filled, overrides the focus/zoom controls.' ) ),
					curie_f( $d, 'crop_grid', 'text', 'Developer crop: Our Doctors card' ),
					curie_f( $d, 'crop_other', 'text', 'Developer crop: "other doctors" card' ),
					curie_f( $d, 'seo_description', 'textarea', 'Search engine description', array( 'rows' => 2, 'new_lines' => '' ) ),
				),
			)
		);

		/* ---------------------------------------------------------------- Location */
		$l = 'location';
		acf_add_local_field_group(
			array(
				'key'            => 'group_curie_location',
				'title'          => 'Location details',
				'location'       => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'location' ) ) ),
				'position'       => 'acf_after_title',
				'style'          => 'seamless',
				'hide_on_screen' => array( 'the_content', 'excerpt', 'featured_image' ),
				'fields'         => array(
					curie_f( $l, 'show_on_contact', 'true_false', 'Curie clinic', array( 'ui' => 1, 'default_value' => 1, 'instructions' => 'On: a Curie clinic, shown as a card on the Contact and Home pages and in the footer. Off: a partner hospital only used as a doctor\'s visiting location.' ) ),
					curie_f( $l, 'eyebrow', 'text', 'Region label', array( 'instructions' => 'Small label above the name, e.g. "Klang Valley".' ) ),
					curie_f( $l, 'footer_label', 'text', 'Footer label', array( 'instructions' => 'Short name in the footer next to the phone number, e.g. "Kuala Lumpur".' ) ),
					curie_f( $l, 'address', 'textarea', 'Address', array( 'rows' => 3, 'new_lines' => '' ) ),
					curie_f( $l, 'address_profile', 'textarea', 'Address on doctor profiles', array( 'rows' => 3, 'new_lines' => '', 'instructions' => 'Optional longer form used in the "Clinic" box on doctor profiles. Falls back to the address above.' ) ),
					curie_f( $l, 'phone', 'text', 'Phone', array( 'placeholder' => '+60 12 822 3310', 'instructions' => 'Write it the way patients should see it; an extension like "ext. 42617" is fine.' ) ),
					curie_f( $l, 'email', 'email', 'Email' ),
					curie_f( $l, 'hours_heading', 'text', 'Hours heading', array( 'instructions' => 'Optional, e.g. "Operating Hours".' ) ),
					curie_f(
						$l,
						'hours',
						'repeater',
						'Opening hours',
						array(
							'layout'       => 'table',
							'button_label' => 'Add row',
							'sub_fields'   => array(
								array( 'name' => 'days', 'label' => 'Days', 'type' => 'text', 'placeholder' => 'Monday to Friday' ),
								array( 'name' => 'time', 'label' => 'Hours', 'type' => 'text', 'placeholder' => '8:30am – 5:30pm' ),
							),
						)
					),
					curie_f( $l, 'closed_note', 'text', 'Closed note', array( 'default_value' => 'Closed on Sunday and Public Holidays' ) ),
					curie_f( $l, 'map_url', 'url', 'Google Maps link', array( 'instructions' => 'Used by the "Get direction" button.' ) ),
					curie_f( $l, 'website', 'url', 'Website', array( 'instructions' => 'For partner hospitals, shown on doctor profiles.' ) ),
					curie_f( $l, 'photo', 'image', 'Photo', array( 'return_format' => 'array', 'preview_size' => 'medium', 'instructions' => 'Landscape photo, at least 1200px wide.' ) ),
					curie_f( $l, 'photo_position', 'text', 'Photo position', array( 'default_value' => '50% 50%', 'instructions' => 'Which part of the photo stays in view, e.g. "50% 40%" (left-right, top-bottom).' ) ),
					curie_f( $l, 'photo_alt', 'text', 'Photo description', array( 'instructions' => 'Describe the photo for visually impaired visitors.' ) ),
				),
			)
		);

		/* ----------------------------------------------------------------- Article */
		$a = 'article';
		acf_add_local_field_group(
			array(
				'key'            => 'group_curie_article',
				'title'          => 'Article',
				'location'       => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'article' ) ) ),
				'position'       => 'acf_after_title',
				'style'          => 'seamless',
				'hide_on_screen' => array( 'the_content', 'excerpt', 'featured_image' ),
				'fields'         => array(
					curie_f( $a, 'image', 'image', 'Image', array( 'return_format' => 'array', 'preview_size' => 'medium', 'required' => 1, 'instructions' => 'Landscape, at least 800px wide.' ) ),
					curie_f( $a, 'image_position', 'text', 'Image focus', array( 'placeholder' => '50% 50%', 'instructions' => 'Optional. Which part of the image stays in view, e.g. "65% 40%" (left-right, top-bottom).' ) ),
					curie_f( $a, 'excerpt', 'textarea', 'Summary', array( 'rows' => 3, 'new_lines' => '', 'required' => 1, 'instructions' => 'One or two sentences. Keep it educational: no promises of outcomes (KKLIU).' ) ),
					curie_f( $a, 'url', 'url', 'Link to full article', array( 'required' => 1 ) ),
					curie_f( $a, 'link_label', 'text', 'Link text', array( 'default_value' => 'Read on Curie Oncology SG' ) ),
				),
			)
		);

		acf_add_local_field_group(
			array(
				'key'      => 'group_curie_topic',
				'title'    => 'Topic',
				'location' => array( array( array( 'param' => 'taxonomy', 'operator' => '==', 'value' => 'topic' ) ) ),
				'fields'   => array(
					curie_f(
						'topic',
						'colour',
						'select',
						'Label colour',
						array(
							'choices'       => array( 'forest' => 'Green', 'honey' => 'Gold', 'clay' => 'Terracotta' ),
							'default_value' => 'forest',
						)
					),
				),
			)
		);

		/* -------------------------------------------------------- Clinic Settings */
		$s = 'settings';
		acf_add_local_field_group(
			array(
				'key'      => 'group_curie_settings',
				'title'    => 'Clinic contact',
				'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'clinic-settings' ) ) ),
				'fields'   => array(
					curie_f( $s, 'main_phone', 'text', 'Main phone', array( 'default_value' => '+60 12 822 3310', 'instructions' => 'Used by the call buttons in banners and on doctor profiles.' ) ),
					curie_f( $s, 'whatsapp', 'text', 'WhatsApp number', array( 'default_value' => '60128223310', 'instructions' => 'Digits only, with country code, e.g. 60128223310.' ) ),
					curie_f( $s, 'main_email', 'email', 'Main email', array( 'default_value' => 'talk2us@curieoncology.com.my' ) ),
				),
			)
		);
	}
);
