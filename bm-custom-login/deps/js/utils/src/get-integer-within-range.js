/**
 * Ensure that the given value is an integer within the defined range
 *
 * @param {string}      value Provided value.
 * @param {number}      min   Minimum valid value.
 * @param {null|number} max   Maximum valid value.
 *
 * @return {number} Integer within the defined range.
 */
export const getIntegerWithinRange = ( value, min, max ) => {
	value = '' === value ? min : Number.parseInt( value, 10 );

	if ( isNaN( value ) || min > value ) {
		value = min;
	}

	if ( null !== max && max < value ) {
		value = max;
	}

	return value;
};
