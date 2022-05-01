<?php

/**
 * Plugin Name: Pronto Elementor Libarian
 * Description: A quick search on Pronto's Elementor library site
 * Author: Pronto Group Ltd.
 * Author URI: https://www.prontomarketing.com
 * Version: 1.0.0
 * Plugin URI: https://github.com/abrudtkuhl/wp-slack-slash-command
 * License: GPL2+
 */

add_action( 'rest_api_init', function () {
	register_rest_route( 'api', '/slash', array(
		'methods'   =>  'POST',
		'callback'  =>  'get_content',
	) );
} ) ;

function get_content() {
	if( isset( $_POST['token'] ) ) {

		// query post by the keyword from Slack command
		if( isset( $_POST['text'] ) ) {
			$keyword       = $_POST['text'];
			$queried_posts = get_posts( array(
				'post_type'      => 'post',
				'orderby'        => 'relevance',
				'posts_per_page' => 5,
				's'              => $keyword,
			) );
		}

		// adjust the response to match with Slack format
		if ( $queried_posts ) {
			$text_response = '';
			foreach ( $queried_posts as $item ) {
				$text_response .= '- ' . $item->post_title . ' ( <' . home_url() . '/' . $item->post_name . '|View> )';
				$text_response .= "\n";
			}
			$response = array(
				"response_type" => "ephemeral",
				"blocks"        => array( array(
					'type' => 'section',
					'text' => [
						'type' => 'mrkdwn',
						'text' => $text_response,
					],
				)),
			);
		} else {
			$response = "Nothing found on library, try again with different keyword.";
		}

		// return message to Slack with JSON header
		header( 'Content-Type: application/json' );
		return $response;
	}

	// error when reach 404?
	return "There is something wrong with the command, please contact platform team if you found this message";
}