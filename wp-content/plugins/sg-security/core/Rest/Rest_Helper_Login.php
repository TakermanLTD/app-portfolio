<?php
namespace SG_Security\Rest;

use SG_Security\Rest\Rest_Helper_Options;
use SG_Security\Sg_2fa\Sg_2fa;
use SG_Security\Login_Service\Login_Service;
use SG_Security\Usernames_Service\Usernames_Service;
use SG_Security\Message_Service\Message_Service;
use SG_Security\Helper\Helper;

/**
 * Rest Helper class that manages the login security.
 */
class Rest_Helper_Login extends Rest_Helper {

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->rest_helper_options = new Rest_Helper_Options();
		$this->sg_2fa              = new Sg_2fa();
		$this->login_service       = new Login_Service();
		$this->usernames_service   = new Usernames_Service();
	}

	/**
	 * Setting custom login URL.
	 *
	 * @since  1.1.0
	 *
	 * @param  object $request Request data.
	 */
	public function custom_login_url( $request ) {
		$data          = json_decode( $request->get_body(), true );
		$login_allowed = intval( get_option( 'users_can_register', false ) );

		// Bail if the both urls are the same.
		if ( $data['login'] === $data['signup'] ) {
			self::send_json(
				__( 'Login and signup URL cannot be the same.', 'sg-security' ),
				0
			);
		}

		// Bail if the login or signup urls are the default urls.
		if (
			'wp-login.php' === $data['login'] ||
			'wp-signup.php' === $data['signup']
		) {
			self::send_json(
				__( 'You cannot use the default URL.', 'sg-security' ),
				0
			);
		}

		update_option( 'sg_security_login_type', $data['type'] );
		update_option( 'sg_security_login_url', str_replace( Helper::get_home_url(), '', $data['login'] ) );
		if ( ! empty( $data['signup'] ) ) {
			update_option( 'sg_security_login_register', str_replace( Helper::get_home_url(), '', $data['signup'] ) );
		}

		self::send_json(
			'Login URL changed!',
			1,
			array(
				'login_url' => array(
					array(
						'type'     => 'custom',
						'label'    => 'Custom',
						'login'    => get_option( 'sg_security_login_url', '' ),
						'signup'   => 1 === $login_allowed ? get_option( 'sg_security_login_register', '' ) : null,
						'selected' => 'custom' === $login_type ? 1 : 0,
					),
					array(
						'type'     => 'default',
						'label'    => 'Default',
						'login'    => wp_login_url(),
						'signup'   => 1 === $login_allowed ? site_url( 'wp-signup.php' ) : null,
						'selected' => 'default' === $login_type ? 1 : 0,
					),
				),
			)
		);
	}


	/**
	 * Update the login access.
	 *
	 * @since  1.0.0
	 *
	 * @param  object $request Request data.
	 */
	public function login_access( $request ) {
		$data = json_decode( $request->get_body(), true );

		update_option( 'sg_login_access', $data );
		self::send_json(
			'Login access updated!',
			1,
			array(
				'login_access' => $data,
			)
		);
	}

	/**
	 * Set the 2fa.
	 *
	 * @since  1.0.0
	 *
	 * @param  object $request Request data.
	 */
	public function sg2fa( $request ) {
		$this->rest_helper_options->change_option_from_rest( $request, 'sg2fa' );
	}

	/**
	 * Reset user 2fa.
	 *
	 * @since  1.1.1
	 *
	 * @param  Object $request The request object.
	 */
	public function reset_user_2fa( $request ) {
		$params = $request->get_params( $request );

		if ( empty( $params['id'] ) ) {
			self::send_json(
				__( 'Missing ID param!', 'sg-security' ),
				0
			);
		}

		$response = $this->sg_2fa->reset_user_2fa( $params['id'] );

		self::send_json(
			$response['message'],
			$response['result'],
			array( 'reset_2fa' => $this->sg_2fa->check_for_users_using_2fa() )
		);
	}

	/**
	 * Disable the admin username.
	 *
	 * @since  1.0.0
	 *
	 * @param  object $request Request data.
	 */
	public function disable_admin_username( $request ) {
		$new_usernames = $this->validate_and_get_option_value( $request, 'admin_names', false );
		$value         = $this->validate_and_get_option_value( $request, 'disable_usernames', false );

		// Check if we need to update any usernames, provided by the user.
		if ( ! empty( $new_usernames ) ) {
			$update_result = $this->usernames_service->update_common_usernames( $new_usernames );
		}

		// Return false, when trying to enable the option, but username updates fail.
		// Add the failed updates array if any.
		if ( ! empty( $update_result ) ) {
			self::send_json(
				Message_Service::get_response_message( 0, 'disable_usernames', 0 ),
				0,
				array(
					'disable_usernames' => 0,
					'usernames_data'    => $update_result,
				)
			);
		}

		$result = $this->change_option( 'disable_usernames', $value );

		// Set the response message.
		self::send_json(
			Message_Service::get_response_message( $result, 'disable_usernames', $value ),
			$result,
			array(
				'disable_usernames' => $value,
			)
		);
	}

	/**
	 * Limit the number of unsuccessful login attempts.
	 *
	 * @since  1.0.0
	 *
	 * @param  object $request Request data.
	 */
	public function limit_login_attempts( $request ) {
		$attempts = intval( $this->validate_and_get_option_value( $request, 'login_attempts' ) );

		update_option( 'sg_security_login_attempts', $attempts );

		delete_option( 'sg_security_unsuccessful_login' );

		self::send_json(
			'Login attempts limited!',
			1,
			$this->login_service->get_login_attempts_data( intval( get_option( 'sg_security_login_attempts', 0 ) ) )
		);
	}
}
