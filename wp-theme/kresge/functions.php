<?php
/**
 * Kresge Carpentry theme bootstrap.
 *
 * @package kresge
 */

foreach ( array( 'setup', 'post-types', 'business-details', 'patterns' ) as $kresge_file ) {
	require_once __DIR__ . "/inc/{$kresge_file}.php";
}
