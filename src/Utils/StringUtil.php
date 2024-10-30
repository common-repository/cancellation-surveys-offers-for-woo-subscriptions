<?php namespace MeowCrew\CancellationOffers\Utils;

class StringUtil {
	
	public static function endsWith( $haystack, $needle ): bool {
		$length = strlen( $needle );
		if ( ! $length ) {
			return true;
		}
		
		return substr( $haystack, - $length ) === $needle;
	}
}
