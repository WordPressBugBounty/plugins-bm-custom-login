/**
 * Monotonic per-bundle counter, combined with the timestamp below so two keys
 * generated within the same millisecond (e.g. adding several rows in quick
 * succession) cannot collide.
 *
 * @type {number}
 */
let sequence = 0;

/**
 * Generate a key for a new `Dynamic_Fields_Group` row.
 *
 * Mirrors the PHP-side contract validated by `Validatable_Fields\Configuration`:
 * a `d:` prefix followed by digits only, 19 characters in total (`d:` + a
 * 13-digit millisecond timestamp + a 4-digit sequence). Centralizes the format
 * so JS-generated keys can't drift from what the PHP group accepts.
 *
 * @return {string} Dynamic field key (e.g. `d:17490000000000001`).
 */
export const generateDynamicFieldKey = () => `d:${ Date.now().toString() }${ String( sequence++ % 10000 ).padStart( 4, '0' ) }`;
