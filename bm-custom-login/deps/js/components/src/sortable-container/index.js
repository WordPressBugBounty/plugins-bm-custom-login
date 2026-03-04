/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { DndContext, closestCenter, KeyboardSensor, PointerSensor, useSensor, useSensors } from '@dnd-kit/core';
import { restrictToVerticalAxis, restrictToWindowEdges } from '@dnd-kit/modifiers';
import { arrayMove, SortableContext, sortableKeyboardCoordinates, useSortable, verticalListSortingStrategy } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';

/**
 * WordPress dependencies
 */
import { Button, Icon, Panel } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { FieldsGroup } from '../fields-group/index.js';

/**
 * Import styles
 */
import './styles.scss';

/**
 * SortableContainerItem component
 *
 * @param {Object}   properties               Component properties object.
 * @param {Object}   properties.context       Additional context object.
 * @param {Object}   properties.data          Item data object.
 * @param {string}   properties.id            Item ID.
 * @param {JSX}      properties.ItemComponent Component to render in a list.
 * @param {Function} properties.onChange      Callback function, used on item data change.
 * @param {Function} properties.onDelete      Callback function, used on item deletion.
 * @param {Object}   properties.presets       Presets to use in components.
 *
 * @return {JSX} SortableContainerItem component.
 */
const SortableContainerItem = ( { context, data, id, ItemComponent, onChange, onDelete, presets } ) => {
	/**
	 * Drag and drop (sortable) related constants
	 */
	const { attributes, listeners, setNodeRef, transform, transition } = useSortable( { id } );
	const style = {
		transform: CSS.Translate.toString( transform ),
		transition,
	};

	/**
	 * Return the item component
	 */
	return (
		<div ref={ setNodeRef } style={ style } className="tsc-sortable-container__item" { ...attributes }>
			<div className="tsc-sortable-container__drag-handle-container" { ...listeners }>
				<Icon icon="menu" className="tsc-sortable-container__drag-handle" />
			</div>
			<ItemComponent context={ context } data={ data } onChange={ onChange } presets={ presets } />
			<Button
				__nextHasNoMarginBottom
				__next40pxDefaultSize
				aria-label={ __( 'Delete item', 'bm-custom-login' ) }
				className="tsc-sortable-container__delete"
				isDestructive
				onClick={ onDelete }
				size="compact"
				variant="secondary"
			>
				<Icon icon="trash" />
			</Button>
		</div>
	);
};

/**
 * Props validation
 */
SortableContainerItem.propTypes = {
	context: PropTypes.object.isRequired,
	data: PropTypes.object.isRequired,
	id: PropTypes.string.isRequired,
	ItemComponent: PropTypes.elementType.isRequired,
	onChange: PropTypes.func.isRequired,
	onDelete: PropTypes.func.isRequired,
	presets: PropTypes.object.isRequired,
};

/**
 * SortableContainer component
 *
 * @param {Object}   properties               Component properties object.
 * @param {Element}  properties.addComponent  Component to use instead of the default "Add new" button.
 * @param {string}   properties.addLabel      Label used on the "Add new" button.
 * @param {Object}   properties.context       Additional context object.
 * @param {JSX}      properties.ItemComponent Component to render in a list.
 * @param {Object}   properties.items         Object of items to render in the sortable container.
 * @param {string}   properties.label         Container's label.
 * @param {Element}  properties.notice        Notice component to render between the label and a sortable list.
 * @param {Function} properties.onAdd         Callback function to trigger when user requested adding a new item.
 * @param {Function} properties.onChange      Callback function to trigger on items change (reorder).
 * @param {Element}  properties.placeholder   Placeholder text to render when there's no any items.
 * @param {Object}   properties.presets       Presets to use in components.
 *
 * @return {JSX} SortableContainer component.
 */
export const SortableContainer = ( {
	addComponent = undefined,
	addLabel = undefined,
	context = {},
	ItemComponent,
	items,
	label,
	notice = undefined,
	onAdd = undefined,
	onChange,
	placeholder,
	presets = {},
} ) => {
	/**
	 * Build the items keys array
	 *
	 * This is required by the sortable context
	 */
	const itemsKeys = Object.keys( items );

	/**
	 * Define drag&drop sensors
	 *
	 * @see https://docs.dndkit.com/api-documentation/sensors
	 */
	const sensors = useSensors(
		useSensor( PointerSensor ),
		useSensor( KeyboardSensor, {
			coordinateGetter: sortableKeyboardCoordinates,
		} )
	);

	/**
	 * Handle the drag end event
	 *
	 * @param {Object} event Event emitted on drag end.
	 */
	const handleDragEnd = ( event ) => {
		const { active, over } = event;

		if ( ! over ) {
			return;
		}

		if ( active.id !== over.id ) {
			const activeIndex = itemsKeys.indexOf( active.id );
			const overIndex = itemsKeys.indexOf( over.id );

			if ( -1 === activeIndex || -1 === overIndex ) {
				return;
			}

			const updatedItemsKeys = arrayMove( itemsKeys, activeIndex, overIndex );

			const updatedItems = {};

			for ( const updatedItemKey of updatedItemsKeys ) {
				updatedItems[ updatedItemKey ] = items[ updatedItemKey ];
			}

			onChange( updatedItems );
		}
	};

	/**
	 * Get the sortable items list
	 */
	const sortableItems = (
		<DndContext
			sensors={ sensors }
			collisionDetection={ closestCenter }
			onDragEnd={ handleDragEnd }
			modifiers={ [ restrictToVerticalAxis, restrictToWindowEdges ] }
		>
			<SortableContext items={ itemsKeys } strategy={ verticalListSortingStrategy }>
				{ itemsKeys.map( ( itemKey ) => (
					<SortableContainerItem
						context={ context }
						data={ items[ itemKey ] }
						id={ itemKey }
						ItemComponent={ ItemComponent }
						key={ itemKey }
						/**
						 * Update the value
						 *
						 * @param {string} updatedValue Updated value.
						 *
						 * @return {void}
						 */
						onChange={ ( updatedValue ) => {
							const updatedItems = Object.assign( {}, items );
							updatedItems[ itemKey ] = updatedValue;

							onChange( updatedItems );
						} }
						/**
						 * Delete one of the items
						 *
						 * @return {void}
						 */
						onDelete={ () => {
							const updatedItems = Object.assign( {}, items );
							delete updatedItems[ itemKey ];

							onChange( updatedItems );
						} }
						presets={ presets }
					/>
				) ) }
			</SortableContext>
		</DndContext>
	);

	/**
	 * Render the component
	 */
	return (
		<FieldsGroup className="tsc-sortable-container" label={ label } withBaseControl>
			{ notice }
			<div className="tsc-sortable-container__actions">
				{ addComponent ? (
					addComponent
				) : (
					<Button __nextHasNoMarginBottom __next40pxDefaultSize onClick={ onAdd } size="compact" variant="secondary">
						{ addLabel ?? __( 'Add new', 'bm-custom-login' ) }
					</Button>
				) }
			</div>
			{
				/**
				 * Render the placeholder
				 */
				0 === itemsKeys.length && <div className="tsc-sortable-container__placeholder">{ placeholder }</div>
			}
			{
				/**
				 * Render the items list within a sortable container
				 */
				0 !== itemsKeys.length && (
					<div className="tsc-sortable-container__items">
						<Panel>{ sortableItems }</Panel>
					</div>
				)
			}
		</FieldsGroup>
	);
};

/**
 * Props validation
 */
SortableContainer.propTypes = {
	addComponent: PropTypes.element,
	addLabel: PropTypes.string,
	context: PropTypes.object,
	ItemComponent: PropTypes.elementType.isRequired,
	items: PropTypes.object.isRequired,
	label: PropTypes.string.isRequired,
	notice: PropTypes.element,
	onAdd: PropTypes.func,
	onChange: PropTypes.func.isRequired,
	placeholder: PropTypes.element.isRequired,
	presets: PropTypes.object.isRequired,
};
