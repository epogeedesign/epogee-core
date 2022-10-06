<?php

function ep_remove_filter($hook, $class, $method, $priority = 0) {
	global $wp_filter;

	// Check if filter exists
	if (!isset($wp_filter[$hook][$priority]) || !is_array($wp_filter[$hook][$priority])) {
		return;
	}

	// Loop over existing filters for the given hook and priority
	foreach ((array)$wp_filter[$hook][$priority] as $id => $filter) {
		// Check if the filter is using class callable array
		if (!isset($filter['function']) || !is_array($filter['function']) || !is_object($filter['function'][0])) {
			continue;
		}

		// Check if the filter class and method match
		if (get_class($filter['function'][0]) == $class && $filter['function'][1] == $method) {
			// Unset the filter
			unset($wp_filter[$hook]->callbacks[$priority][$id]);
		}
	}
}

function ep_get_filters($hook) {
	global $wp_filter;

	// Check if filter exists and has callbacks
	if (!isset($wp_filter[$hook]->callbacks)) {
		return [];
	}

	// Find available hooks
	$hooks = [];
	array_walk($wp_filter[$hook]->callbacks, function($callbacks, $priority) use (&$hooks) {           
		foreach ($callbacks as $id => $callback) {
			// Skip if callback does not exist
			if (!is_callable($callback['function'])) {
				continue;
			}

			// Add to available hooks
			$hooks[] = array_merge([
				'id' => $id,
				'priority' => $priority
			], $callback);
		}
	}); 

	// Iterate through available hooks
	foreach ($hooks as &$filter) {
		// Check if callback is a function
		if (is_string($filter['function'])) {
			// Get reflection
			$pos = strpos($filter['function'], '::');
			$reflect = $pos ? new ReflectionClass(substr($filter['function'], 0, $pos)) : new ReflectionFunction($filter['function']);

			// Add info to filter
			$filter['file'] = $reflect->getFileName();
			$filter['line'] = $pos
				? $reflect->getMethod(substr($filter['function'], $pos + 2))->getStartLine()
				: $reflect->getStartLine();
		}

		// Check if callback is an array
		else if (is_array($filter['function'])) {
			// Get reflection
			$pos = strpos($filter['function'][1], '::');
			$reflect = new ReflectionClass($filter['function'][0]);

			// Add info to filter
			$filter['function'] = [
				is_object($filter['function'][0]) ? get_class($filter['function'][0]) : $filter['function'][0],
				$filter['function'][1]
			];
			$filter['file'] = $reflect->getFileName();
			$filter['line'] = $pos
				? $reflect->getParentClass()->getMethod(substr($filter['function'][1], $pos + 2))->getStartLine()
				: $reflect->getMethod($filter['function'][1])->getStartLine();
		}

		// Check if callback is an anonymous closure
		else if (is_callable($filter['function'])) {
			// Get reflection
			$reflect = new ReflectionFunction($filter['function']);

			// Add info to filter
			$filter['function'] = get_class($filter['function']);
			$filter['file'] = $reflect->getFileName();
			$filter['line'] = $reflect->getStartLine();
		}

	}

	return $hooks;
}
