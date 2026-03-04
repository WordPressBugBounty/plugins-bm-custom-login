/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { sprintf } from '@wordpress/i18n';
import { G, Path, Rect, SVG } from '@wordpress/primitives';

/**
 * Import styles
 */
import './styles.scss';

/**
 * ProductIcon component
 *
 * @param {Object} properties      Component properties object.
 * @param {string} properties.slug Product's slug.
 *
 * @return {JSX} ProductIcon component.
 */
export const ProductIcon = ( { slug } ) => {
	// Declare the SVG prefix.
	const svgPrefix = `ts-product-icon-${ slug }`;

	/**
	 * Render the product-specific icon
	 */
	switch ( slug ) {
		// WP Custom Login plugin icon.
		case 'bm-custom-login':
			return (
				<div className="tsc-product-icon">
					<SVG fill="none" height="256" viewBox="0 0 256 256" width="256" xmlns="http://www.w3.org/2000/svg">
						<Path
							fill="#ffe8db"
							d="M186.182 0H69.818C31.258 0 0 31.259 0 69.818v116.364C0 224.741 31.259 256 69.818 256h116.364C224.741 256 256 224.741 256 186.182V69.818C256 31.258 224.741 0 186.182 0"
						/>
						<Path
							fill="#ffd7c1"
							d="M85.33 213.329a42.7 42.7 0 0 1-42.66-42.659V85.33a42.706 42.706 0 0 1 42.66-42.66h85.34a42.705 42.705 0 0 1 42.659 42.66v85.34a42.7 42.7 0 0 1-42.659 42.659z"
						/>
						<Path
							fill="#ff9e68"
							d="M96 192a32.035 32.035 0 0 1-32-32V96a32.035 32.035 0 0 1 32-32h64a32.034 32.034 0 0 1 32 32v64a32.034 32.034 0 0 1-32 32z"
						/>
						<Path
							fill="#dd3814"
							d="M128.652 163.77a5.34 5.34 0 0 1-1.563-3.77 5.33 5.33 0 0 1 1.563-3.77l24.448-24.46a5.34 5.34 0 0 0 0-7.54l-24.448-24.46a5.332 5.332 0 1 1 7.54-7.54l24.448 24.46a16.01 16.01 0 0 1 0 22.621l-24.448 24.459a5.31 5.31 0 0 1-7.54 0"
						/>
						<Path
							fill="#dd3814"
							d="M159.942 133.33H42.671a5.33 5.33 0 0 1-4.924-3.29 5.33 5.33 0 0 1 1.155-5.808 5.327 5.327 0 0 1 3.769-1.561h117.271a5.33 5.33 0 0 1 0 10.659"
						/>
						<Path
							fill="#dd3814"
							d="M170.671 218.671H85.33a48.06 48.06 0 0 1-48-48 5.337 5.337 0 0 1 9.108-3.773A5.34 5.34 0 0 1 48 170.671 37.376 37.376 0 0 0 85.33 208h85.341A37.375 37.375 0 0 0 208 170.671V85.33A37.374 37.374 0 0 0 170.671 48H85.33A37.376 37.376 0 0 0 48 85.33a5.336 5.336 0 0 1-10.67 0 48.06 48.06 0 0 1 48-48h85.341a48.06 48.06 0 0 1 48 48v85.341a48.06 48.06 0 0 1-14.076 33.924 48.06 48.06 0 0 1-33.924 14.076"
						/>
					</SVG>
				</div>
			);

		// Careers theme icon.
		case 'careers':
			return (
				<div className="tsc-product-icon">
					<SVG fill="none" height="256" viewBox="0 0 256 256" width="256" xmlns="http://www.w3.org/2000/svg">
						<G clipPath={ sprintf( 'url(#%s-a)', svgPrefix ) }>
							<Rect fill="#fff" height="256" rx="10.24" width="256" />
							<Path d="M0 0h256v256H0z" fill="#AD1735" fillOpacity=".05" />
							<Path
								d="M130.289 201.349c-14.461 0-27.185-3.062-38.172-9.188-10.988-6.188-19.576-14.776-25.764-25.763-6.189-10.988-9.283-23.744-9.283-38.267 0-10.988 1.8-21.06 5.4-30.216 3.599-9.156 8.682-17.081 15.25-23.775 6.63-6.693 14.428-11.871 23.395-15.534 9.03-3.662 18.975-5.494 29.837-5.494 7.83 0 15.628 1.01 23.396 3.032 7.767 1.957 14.713 4.799 20.838 8.524h.758l8.146-9.567h5.683v45.845h-8.904c-1.263-3.347-2.589-6.44-3.978-9.282-1.326-2.842-2.779-5.431-4.357-7.768-4.357-6.756-9.535-11.713-15.534-14.87-5.999-3.221-12.914-4.831-20.744-4.831-14.461 0-25.385 5.21-32.773 15.629-7.325 10.356-10.988 25.826-10.988 46.412 0 20.712 3.663 36.531 10.988 47.455 7.388 10.861 18.06 16.292 32.015 16.292 7.704 0 14.65-1.674 20.839-5.02 6.251-3.347 11.461-8.083 15.628-14.208 2.211-3.095 4.105-6.599 5.684-10.514 1.641-3.978 2.873-8.272 3.694-12.882h8.43v52.001h-7.009l-7.578-12.313c-7.325 4.988-14.587 8.619-21.786 10.892-7.135 2.274-14.839 3.41-23.111 3.41Z"
								fill="#AD1735"
							/>
						</G>
						<defs>
							<clipPath id={ sprintf( '%s-a', svgPrefix ) }>
								<Rect fill="#fff" height="256" rx="10.24" width="256" />
							</clipPath>
						</defs>
					</SVG>
				</div>
			);

		// Hiring Center plugin icon.
		case 'hiring-center':
			return (
				<div className="tsc-product-icon">
					<SVG fill="none" height="256" viewBox="0 0 256 256" width="256" xmlns="http://www.w3.org/2000/svg">
						<mask
							height="256"
							id={ sprintf( '%s-a', svgPrefix ) }
							maskUnits="userSpaceOnUse"
							style={ { maskType: 'alpha' } }
							width="256"
							x="0"
							y="0"
						>
							<Path fill="#fff" d="M0 0h256v256H0z" />
						</mask>
						<G mask={ sprintf( 'url(#%s-a)', svgPrefix ) }>
							<Path
								fill="#111"
								d="M-.296 10.24C-.296 4.585 4.29 0 9.944 0h235.52c5.656 0 10.24 4.585 10.24 10.24v235.52c0 5.655-4.584 10.24-10.24 10.24H9.944c-5.655 0-10.24-4.585-10.24-10.24V10.24Z"
							/>
							<Path
								fill="#fcebd8"
								d="m279.269 249.611-44.688-44.688a15.895 15.895 0 0 0-12.186-4.598l-18.774-18.773a90.092 90.092 0 0 0 16.326-51.76c0-49.995-40.672-90.672-90.672-90.672s-90.662 40.677-90.662 90.677c0 50 40.672 90.672 90.672 90.672a90.13 90.13 0 0 0 51.76-16.325l18.774 18.773a15.884 15.884 0 0 0 4.597 12.182l44.688 44.688a15.947 15.947 0 0 0 11.317 4.677c4.102 0 8.192-1.557 11.312-4.677l7.542-7.542a16.015 16.015 0 0 0 0-22.629l-.006-.005ZM49.279 129.797c0-44.112 35.889-80 80.001-80s80 35.888 80 80-35.888 80-80 80-80-35.888-80-80Zm147.66 60.155 14.992 14.992-7.499 7.499-14.992-14.992a90.554 90.554 0 0 0 7.499-7.499Zm74.784 74.741-7.542 7.542a5.354 5.354 0 0 1-7.546 0l-44.688-44.688a5.345 5.345 0 0 1 0-7.547l7.541-7.541a5.347 5.347 0 0 1 7.547 0l44.688 44.688a5.343 5.343 0 0 1 0 7.546Z"
							/>
							<Path
								fill="#fcebd8"
								d="M129.28 60.459c-38.23 0-69.333 31.104-69.333 69.333s31.104 69.333 69.333 69.333 69.333-31.104 69.333-69.333c0-38.23-31.104-69.333-69.333-69.333Zm-32 118.442v-6.442c0-14.704 11.963-26.667 26.667-26.667h10.666c14.704 0 26.667 11.963 26.667 26.667v6.442c-9.211 6.027-20.192 9.558-32 9.558s-22.789-3.536-32-9.558Zm32-49.109c-8.821 0-16-7.179-16-16s7.179-16 16-16 16 7.179 16 16-7.179 16-16 16Zm42.544 40.277a37.354 37.354 0 0 0-28.288-33.818 26.618 26.618 0 0 0 12.411-22.454c0-14.704-11.963-26.666-26.667-26.666-14.704 0-26.667 11.962-26.667 26.666a26.608 26.608 0 0 0 3.322 12.821 26.607 26.607 0 0 0 9.089 9.633 37.348 37.348 0 0 0-28.288 33.818 58.399 58.399 0 0 1-16.123-40.277c0-32.347 26.32-58.667 58.667-58.667s58.667 26.32 58.667 58.667c0 15.595-6.16 29.755-16.123 40.277Z"
							/>
						</G>
					</SVG>
				</div>
			);

		// WP Password Policy plugin icon.
		case 'password-requirements':
			return (
				<div className="tsc-product-icon">
					<SVG fill="none" height="256" viewBox="0 0 500 500" width="256" xmlns="http://www.w3.org/2000/svg">
						<Path fill="#F0F2FF" d="M113 113h274v274H113z" />
						<G clipPath={ sprintf( 'url(#%s-a)', svgPrefix ) } filter={ sprintf( 'url(#%s-b)', svgPrefix ) }>
							<Path
								fill="#0E38F4"
								d="M473 228.584V118.62a60.596 60.596 0 0 0-34.724-51.747L298.577 9.685a129.191 129.191 0 0 0-98.154 0L60.724 66.873A60.597 60.597 0 0 0 26 118.62v109.964a289.053 289.053 0 0 0 210.601 271.123 48.481 48.481 0 0 0 25.673 0 289.05 289.05 0 0 0 210.601-271.123H473Zm-204.684 44.001v65.888a18.805 18.805 0 0 1-18.803 18.803 18.805 18.805 0 0 1-18.804-18.803v-65.888a62.675 62.675 0 0 1-22.016-107.357 62.683 62.683 0 0 1 81.639 0 62.675 62.675 0 0 1-22.016 107.357Z"
							/>
						</G>
						<defs>
							<clipPath id={ sprintf( '%s-a', svgPrefix ) }>
								<path fill="#fff" d="M0 0h500v500H0z" />
							</clipPath>
							<filter
								id={ sprintf( '%s-b', svgPrefix ) }
								width="447"
								height="529.754"
								x="26"
								y="0"
								colorInterpolationFilters="sRGB"
								filterUnits="userSpaceOnUse"
							>
								<feFlood floodOpacity="0" result="BackgroundImageFix" />
								<feBlend in="SourceGraphic" in2="BackgroundImageFix" result="shape" />
								<feColorMatrix in="SourceAlpha" result="hardAlpha" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" />
								<feOffset dy="28.316" />
								<feGaussianBlur stdDeviation="14.158" />
								<feComposite in2="hardAlpha" k2="-1" k3="1" operator="arithmetic" />
								<feColorMatrix values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.15 0" />
								<feBlend in2="shape" result="effect1_innerShadow_171_139" />
							</filter>
						</defs>
					</SVG>
				</div>
			);

		// Password Reset Enforcement plugin icon.
		case 'password-reset-enforcement':
			return (
				<div className="tsc-product-icon">
					<SVG fill="none" height="256" viewBox="0 0 256 256" width="256" xmlns="http://www.w3.org/2000/svg">
						<clipPath id={ sprintf( '%s-b', svgPrefix ) }>
							<Path d="M31.744 31.744h192.512v192.512H31.744z" />
						</clipPath>
						<mask height="256" id={ sprintf( '%s-a', svgPrefix ) } maskUnits="userSpaceOnUse" width="256" x="0" y="0">
							<Path d="M0 0h256v256H0z" fill="#fff" />
						</mask>
						<mask height="194" id={ sprintf( '%s-c', svgPrefix ) } maskUnits="userSpaceOnUse" width="194" x="31" y="31">
							<Path d="M31.744 31.744h192.512v192.512H31.744z" fill="#fff" />
						</mask>
						<G mask={ sprintf( 'url(#%s-a)', svgPrefix ) }>
							<Rect fill="#111" height="256" rx="10.24" width="256" x="-.296" />
							<G clipPath={ sprintf( 'url(#%s-b)', svgPrefix ) } mask={ sprintf( 'url(#%s-c)', svgPrefix ) }>
								<Path
									d="m215.503 116.44 4.907-29.664-7.994 3.432c-9.869-22.07-27.615-39.125-50.118-48.11-22.945-9.161-48.085-8.839-70.788.908-22.702 9.747-40.25 27.751-49.412 50.696l13.968 5.577c7.672-19.214 22.366-34.291 41.377-42.452 19.012-8.163 40.064-8.433 59.278-.761 18.773 7.495 33.592 21.697 41.875 40.076l-7.979 3.426zM40.497 139.56l-4.907 29.664 7.994-3.432c9.87 22.069 27.615 39.125 50.119 48.11 22.945 9.161 48.085 8.839 70.787-.908 22.703-9.747 40.251-27.751 49.412-50.696l-13.967-5.577c-7.672 19.214-22.367 34.291-41.378 42.452-19.011 8.163-40.063 8.433-59.277.761-18.773-7.495-33.593-21.697-41.876-40.076l7.98-3.426z"
									stroke="#fcebd8"
									strokeLinecap="round"
									strokeLinejoin="round"
									strokeMiterlimit="10"
									strokeWidth="7.52"
								/>
								<Path
									d="M92.526 101.467a6.31 6.31 0 0 1 0-8.928 6.317 6.317 0 0 1 8.931 0 6.312 6.312 0 0 1 0 8.928 6.316 6.316 0 0 1-8.931 0z"
									fill="#fcebd8"
								/>
								<G stroke="#fcebd8" strokeLinecap="round" strokeLinejoin="round" strokeMiterlimit="10" strokeWidth="7.52">
									<Path d="m131.753 116.968 40.931 40.916-1.055 13.724-13.729 1.056-4.755-4.753-2.357-7.497-3.52-3.519-7.03-1.886-4.577-4.575-1.417-6.558-4.107-4.106-7.148-2.004-6.021-6.018" />
									<Path d="M84.693 125.302c-11.214-11.209-11.214-29.383 0-40.593 11.213-11.209 29.394-11.209 40.607 0 11.214 11.21 11.214 29.384 0 40.593-11.213 11.209-29.394 11.209-40.607 0z" />
								</G>
							</G>
						</G>
					</SVG>
				</div>
			);
	}

	// "Teydea Studio" logo as a default fallback.
	return (
		<div className="tsc-product-icon">
			<SVG fill="none" height="256" viewBox="0 0 256 256" width="256" xmlns="http://www.w3.org/2000/svg">
				<G clipPath={ sprintf( 'url(#%s-a)', svgPrefix ) }>
					<mask height="256" id={ sprintf( '%s-b', svgPrefix ) } maskUnits="userSpaceOnUse" style={ { maskType: 'alpha' } } width="256" x="0" y="0">
						<Path d="M0 0h256v256H0z" fill="#fff" />
					</mask>
					<G mask={ sprintf( 'url(#%s-b)', svgPrefix ) }>
						<Rect fill="#FCEBD8" height="256" rx="10.24" width="256" x="-.296" />
						<G clipPath={ sprintf( 'url(#%s-c)', svgPrefix ) }>
							<Path
								d="M81.86 202.714c-4.212-5.711-9.207-10.766-14.743-15.179L10.774 194a120.343 120.343 0 0 0 19.781 9.306l.156.057c9.356 3.391 18.15 8.218 25.745 14.647 20.947 17.728 49.576 24.847 76.933 18.099-20.629-4.497-39.01-16.421-51.529-33.395ZM184.644 94.535c7.905 0 15.05 2.449 20.245 6.396-2.678-9.353-9.32-17.45-18.663-21.637-18.175-8.145-39.105.935-45.795 19.315-3.232 8.313-5.907 22.207-5.907 22.207l-.01.003a31.663 31.663 0 0 1 13.381 12.506c3.764 6.46 4.956 13.483 4.08 20.112l16.993-10.72c3.04-1.918 2.786-6.373-.407-8.023-7.636-3.946-12.656-10.622-12.656-18.198 0-12.128 12.867-21.96 28.739-21.96Z"
								fill="#111"
							/>
							<Path
								d="m112.219 157.765-79.112 13.21"
								stroke="#111"
								strokeLinecap="round"
								strokeLinejoin="round"
								strokeMiterlimit="10"
								strokeWidth="10.333"
							/>
							<Path
								d="M147.895 133.326c-9.345-16.041-30.373-20.725-45.642-10.166l-30.074 20.796c2.197 4.576 9.653 6.09 18.016 4.029-.672 1.9-.731 3.753-.054 5.411 2.01 4.922 9.842 6.537 18.577 4.271-.736 1.967-.826 3.888-.126 5.602 2.208 5.403 11.432 6.823 21.164 3.495-1.22 2.397-1.527 4.777-.679 6.852.845 2.069 2.721 3.55 5.258 4.408 15.986-7.75 23.236-28.09 13.56-44.698Z"
								fill="#111"
							/>
							<Path
								d="m46.684 211.546-59.718 23.285c-9.123 3.557-15.71-8.949-7.618-14.461l34.97-23.818M145.995 232.995l14.599 28.633M120.83 238.624l11.729 23.004"
								stroke="#111"
								strokeLinecap="round"
								strokeLinejoin="round"
								strokeMiterlimit="10"
								strokeWidth="10.333"
							/>
							<Path
								d="M15.902 196.848a120.77 120.77 0 0 0 14.653 6.458l.156.057c9.356 3.391 18.149 8.218 25.744 14.646 25.087 21.233 61.194 27.253 92.936 12.452 32.427-15.121 51.137-47.701 50.164-81.328-.264-9.138 1.122-18.257 4.393-26.793a34.657 34.657 0 0 0 1.162-3.619c4.162-15.903-3.882-32.705-18.884-39.427-18.175-8.145-39.105.935-45.795 19.315l-6.662 20.343"
								stroke="#111"
								strokeLinecap="round"
								strokeLinejoin="round"
								strokeMiterlimit="10"
								strokeWidth="10.333"
							/>
							<Path
								d="m206.525 101.23 15.166 7.023-15.489 7.744M-2.372 195.508l104.625-72.348c15.269-10.559 36.297-5.876 45.642 10.165 11.57 19.86-1.059 45.063-23.893 47.683l-126.374 14.5Z"
								stroke="#111"
								strokeLinecap="round"
								strokeLinejoin="round"
								strokeMiterlimit="10"
								strokeWidth="10.333"
							/>
						</G>
					</G>
				</G>
				<defs>
					<clipPath id={ sprintf( '%s-a', svgPrefix ) }>
						<Path d="M0 0h256v256H0z" fill="#fff" />
					</clipPath>
					<clipPath id={ sprintf( '%s-c', svgPrefix ) }>
						<Path fill="#fff" transform="translate(-29.31 40.96)" d="M0 0h256v256H0z" />
					</clipPath>
				</defs>
			</SVG>
		</div>
	);
};

/**
 * Props validation
 */
ProductIcon.propTypes = {
	slug: PropTypes.string.isRequired,
};
