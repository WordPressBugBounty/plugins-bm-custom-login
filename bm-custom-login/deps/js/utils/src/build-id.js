/**
 * Build the ID based on a given partials
 *
 * @param {string} plugin    Plugin's slug.
 * @param {string} component Component's slug.
 * @param {string} element   Element's slug.
 *
 * @return {string} Built ID.
 */
export const buildId = ( plugin, component, element ) => `${ plugin }-${ component }-${ element }`;
