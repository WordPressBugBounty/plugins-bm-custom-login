/**
 * WordPress dependencies
 */
import { createRoot } from '@wordpress/element';

/**
 * Render the component in a given node
 *
 * @param {JSX}  component Component to render.
 * @param {Node} node      Node to use as a root element.
 *
 * @return {void}
 */
export const render = ( component, node ) => {
	const root = createRoot( node );
	root.render( component );
};
