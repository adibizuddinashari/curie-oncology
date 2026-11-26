<?php
/**
 * Website enquiries: the "Drop Us a Message" forms (Home, Contact) and the
 * "Book a consultation" modal on doctor profiles.
 *
 * Each submission is emailed to the clinic (Clinic Settings → Main email)
 * and also saved under "Enquiries" in the admin, so nothing is lost if an
 * email lands in spam. Spam protection: hidden honeypot field + minimum
 * fill time (no nonce, so cached pages keep working).
 */

defined( 'ABSPATH' ) || exit;

/* Stored copy of each enquiry (private: admin + Clinic Editor only). */
add_action(
	'init',
	function () {
		register_post_type(
			'enquiry',
			array(
				'labels'          => array(
					'name'          => 'Enquiries',
					'singular_name' => 'Enquiry',
					'edit_item'     => 'Enquiry',
					'all_items'     => 'All enquiries',
					'not_found'     => 'No enquiries yet',
				),
				'public'          => false,
				'show_ui'         => true,
				'menu_position'   => 4,
				'menu_icon'       => 'dashicons-email-alt',
				'supports'        => array( 'title', 'editor' ),
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'    => true,
				'show_in_rest'    => false,
			)
		);
	}
);

/** Hidden fields printed inside every enquiry form. */
function curie_form_fields( $type ) {
	printf(
		'<input type="hidden" name="action" value="curie_enquiry" /><input type="hidden" name="form_type" value="%s" /><input type="hidden" name="form_ts" value="%d" /><div aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden"><label>Leave this empty<input type="text" name="website" tabindex="-1" autocomplete="off" /></label></div>',
		esc_attr( $type ),
		time()
	);
}

add_action( 'admin_post_nopriv_curie_enquiry', 'curie_handle_enquiry' );
add_action( 'admin_post_curie_enquiry', 'curie_handle_enquiry' );

function curie_handle_enquiry() {
	$ajax = isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && 'fetch' === $_SERVER['HTTP_X_REQUESTED_WITH'];
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/' );

	$respond = function ( $ok, $message ) use ( $ajax, $back ) {
		if ( $ajax ) {
			wp_send_json( array( 'ok' => $ok, 'message' => $message ), $ok ? 200 : 400 );
		}
		wp_safe_redirect( add_query_arg( 'enquiry', $ok ? 'sent' : 'error', remove_query_arg( 'enquiry', $back ) ) . '#message-form' );
		exit;
	};

	// Bots: filled honeypot or submitted faster than a person can type. Pretend success.
	$elapsed = time() - (int) ( $_POST['form_ts'] ?? 0 );
	if ( ! empty( $_POST['website'] ) || $elapsed < 3 ) {
		$respond( true, 'Thank you. Our team will be in touch shortly.' );
	}

	$type    = 'booking' === ( $_POST['form_type'] ?? '' ) ? 'booking' : 'message';
	$name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$phone   = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
	$message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );
	$slug    = sanitize_title( wp_unslash( $_POST['doctor'] ?? '' ) );
	$pdpa    = ! empty( $_POST['pdpa'] );

	if ( '' === $name || ! is_email( $email ) || ! $pdpa || ( 'booking' === $type && '' === $phone ) ) {
		$respond( false, 'Please fill in the required fields and tick the consent box.' );
	}

	$doctor = '';
	if ( $slug ) {
		$post   = get_page_by_path( $slug, OBJECT, 'doctor' );
		$doctor = $post ? get_the_title( $post ) : '';
	}

	$subject = 'booking' === $type
		? 'Appointment request' . ( $doctor ? " – $doctor" : '' ) . " – $name"
		: "Website message – $name";

	$lines = array(
		'Type'             => 'booking' === $type ? 'Appointment request' : 'Message',
		'Name'             => $name,
		'Email'            => $email,
		'Phone'            => $phone,
		'Preferred doctor' => $doctor ? $doctor : 'No preference',
		'Message'          => $message,
		'PDPA consent'     => 'Yes',
		'Sent from'        => $back,
		'Received'         => wp_date( 'j M Y, g:i a' ),
	);
	$body = '';
	foreach ( $lines as $label => $value ) {
		if ( '' !== $value ) {
			$body .= "$label: $value\n";
		}
	}

	wp_insert_post(
		array(
			'post_type'    => 'enquiry',
			'post_status'  => 'private',
			'post_title'   => $subject,
			'post_content' => $body,
		)
	);

	$to = curie_setting( 'main_email', 'talk2us@curieoncology.com.my' );
	wp_mail( $to, $subject, $body, array( 'Reply-To: ' . $name . ' <' . $email . '>' ) );

	$respond( true, 'Thank you. Our team will be in touch shortly.' );
}

/* Submit forms without leaving the page; show the result in place. */
add_action(
	'wp_footer',
	function () {
		?>
<script>
  (function () {
    document.querySelectorAll('form[data-curie-form]').forEach(function (form) {
      var note = document.createElement('p');
      note.setAttribute('role', 'status');
      note.className = 'hidden rounded-[14px] bg-frost px-4 py-3 text-[15px] leading-6 tracking-body text-forest';
      form.appendChild(note);
      if (/[?&]enquiry=sent/.test(location.search)) { note.textContent = 'Thank you. Our team will be in touch shortly.'; note.classList.remove('hidden'); }
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        var button = form.querySelector('[type="submit"]');
        if (button) button.disabled = true;
        // getAttribute: the hidden input named "action" shadows form.action.
        fetch(form.getAttribute('action'), { method: 'POST', body: new FormData(form), headers: { 'X-Requested-With': 'fetch' } })
          .then(function (r) { return r.json(); })
          .then(function (res) {
            note.textContent = res.message;
            note.classList.remove('hidden');
            if (res.ok) form.querySelectorAll('input:not([type=hidden]), textarea').forEach(function (f) { if (f.type === 'checkbox') f.checked = false; else f.value = ''; });
          })
          .catch(function () {
            note.textContent = 'Sorry, something went wrong. Please call or WhatsApp us instead.';
            note.classList.remove('hidden');
          })
          .finally(function () { if (button) button.disabled = false; });
      });
    });
  })();
</script>
		<?php
	},
	50
);
