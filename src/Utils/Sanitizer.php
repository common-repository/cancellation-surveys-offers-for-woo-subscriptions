<?php namespace MeowCrew\CancellationOffers\Utils;

class Sanitizer {
	
	public static function sanitizeIntegerArray( $array ): array {
		if ( ! is_array( $array ) ) {
			return array();
		}
		
		return array_filter( array_map( 'intval', $array ) );
	}
	
	public static function sanitizeStringArray( $array ): array {
		if ( ! is_array( $array ) ) {
			return array();
		}
		
		return array_filter( array_map( 'strval', $array ) );
	}
	
	public static function sanitizeArray( $array ): array {
		if ( ! is_array( $array ) ) {
			return array();
		}
		
		return $array;
	}
}
