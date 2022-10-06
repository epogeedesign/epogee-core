<?php

function ep_rest_request($method = 'GET', $endpoint = '', $query = [], $body = []) {
	$request = new WP_REST_Request($method, $endpoint);

	if (!empty($query))
		$request->set_query_params($query);

	if (!empty($body))
		$request->set_body_params($body);

	$response = rest_do_request($request);
	$server = rest_get_server();

	return $server->response_to_data($response, false);
}
