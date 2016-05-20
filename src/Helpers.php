<?php

	/**
	 * Generate a UUID
	 */
	if( !function_exists( 'generate_uuid' ) )
	{
		function generate_uuid( $prefix = false, $braces = false )
		{
			mt_srand( (double) microtime() * 10000 );
			$charid = strtoupper( md5( uniqid( $prefix === false ? rand() : $prefix, true ) ) );
			$hyphen = chr( 45 ); // "-"
			$uuid   = substr( $charid, 0, 8 ) . $hyphen
				. substr( $charid, 8, 4 ) . $hyphen
				. substr( $charid, 12, 4 ) . $hyphen
				. substr( $charid, 16, 4 ) . $hyphen
				. substr( $charid, 20, 12 );

			// Add brackets or not? "{" ... "}"
			return $braces ? chr( 123 ) . $uuid . chr( 125 ) : $uuid;
		}
	}

	/**
	 * Check for valid UUID
	 */
	if( !function_exists( 'is_uuid' ) )
	{
		function is_uuid( $uuid )
		{
			if( preg_match( '/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', $uuid ) )
			{
				return true;
			}

			return false;
		}
	}

//	if( !function_exists( 'input_old' ) )
//	{
//		function input_old( $old, $default = null )
//		{
//			return \Illuminate\Support\Facades\Input::old( $old, $default );
//		}
//	}

