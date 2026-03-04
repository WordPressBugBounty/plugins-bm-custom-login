<?php
/**
 * The "Date_Time" class, responsible for date and time utilities
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

use DateTime;
use DateTimeZone;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Date_Time" class
 */
final class Date_Time {
	/**
	 * Get the DateTime object relative to the given timezone,
	 * optionally modified by a given modifier
	 *
	 * @param DateTimeZone $timezone  Timezone object.
	 * @param int|string   $timestamp Optional timestamp or 'now' string.
	 * @param string       $modifier  Optional modifier to apply (e.g., '-1 day', '+2 hours').
	 *
	 * @return DateTime DateTime object.
	 */
	public static function get_datetime( DateTimeZone $timezone, $timestamp = 'now', string $modifier = '' ): DateTime {
		$datetime = new DateTime( 'now', $timezone );

		if ( 'now' !== $timestamp ) {
			$datetime->setTimestamp( Type::ensure_int( $timestamp ) );
		}

		$modifier = Strings::trim( $modifier );

		if ( ! empty( $modifier ) ) {
			$datetime->modify( $modifier );
		}

		return $datetime;
	}

	/**
	 * Get internationalized string representation of the date in the
	 * site's timezone, based on the given format and timestamp
	 *
	 * @param int|string $timestamp   Optional timestamp or 'now' string.
	 * @param string     $date_format Date format (e.g., 'Y-m-d'), defaults to WordPress date format.
	 * @param string     $time_format Time format (e.g., 'H:i:s'), defaults to WordPress time format.
	 *
	 * @return string Formatted date string.
	 */
	public static function get_i18n_datetime_string( $timestamp = 'now', string $date_format = 'site', string $time_format = 'site' ): string {
		if ( 'site' === $date_format ) {
			$date_format = Type::ensure_string( get_option( 'date_format', 'F j, Y' ) );
		}

		if ( 'site' === $time_format ) {
			$time_format = Type::ensure_string( get_option( 'time_format', 'g:i a' ) );
		}

		$format   = Strings::trim( sprintf( '%1$s %2$s', $date_format, $time_format ) );
		$datetime = self::get_site_datetime( $timestamp );

		if ( 'now' === $timestamp ) {
			$timestamp = $datetime->getTimestamp();
		} else {
			$timestamp = Type::ensure_int( $timestamp );
		}

		$timestamp += $datetime->getOffset();

		return date_i18n( $format, $timestamp );
	}

	/**
	 * Get the DateTime object relative to the site's timezone,
	 * optionally modified by a given modifier
	 *
	 * @param int|string $timestamp Optional timestamp or 'now' string.
	 * @param string     $modifier  Optional modifier to apply (e.g., '-1 day', '+2 hours').
	 *
	 * @return DateTime DateTime object.
	 */
	public static function get_site_datetime( $timestamp = 'now', string $modifier = '' ): DateTime {
		return self::get_datetime( wp_timezone(), $timestamp, $modifier );
	}

	/**
	 * Get the timestamp relative to the UTC timezone,
	 * optionally modified by a given modifier
	 *
	 * @param string $modifier Optional modifier to apply (e.g., '-1 day', '+2 hours').
	 *
	 * @return int UTC timestamp.
	 */
	public static function get_utc_timestamp( string $modifier = '' ): int {
		$timestamp = time();
		$modifier  = Strings::trim( $modifier );

		if ( ! empty( $modifier ) ) {
			$timestamp = strtotime( $modifier, $timestamp );

			if ( false === $timestamp ) {
				// If the modifier is invalid, return the current timestamp.
				$timestamp = time();
			}
		}

		return $timestamp;
	}

	/**
	 * Get the DateTime object relative to the UTC timezone,
	 * optionally modified by a given modifier
	 *
	 * @param int|string $timestamp Optional timestamp or 'now' string.
	 * @param string     $modifier  Optional modifier to apply (e.g., '-1 day', '+2 hours').
	 *
	 * @return DateTime DateTime object.
	 */
	public static function get_utc_datetime( $timestamp = 'now', string $modifier = '' ): DateTime {
		return self::get_datetime( new DateTimeZone( '+00:00' ), $timestamp, $modifier );
	}
}
