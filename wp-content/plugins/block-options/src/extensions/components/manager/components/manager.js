/**
 * External dependencies
 */
import map from 'lodash/map';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { withSelect, withDispatch, select } = wp.data;
const { compose } = wp.compose;
const { Fragment, Component } = wp.element;
const { PluginMoreMenuItem } = wp.editPost;
const { withSpokenMessages, Modal, CheckboxControl } = wp.components;

const capitalize = ( str ) => {
	return str.charAt( 0 ).toUpperCase() + str.slice( 1 );
};

/**
 * Render plugin
 */
class FeaturesManager extends Component {
	constructor() {
		super( ...arguments );

		this.state = {
			isOpen: false,
		};
	}

	render() {
		const {
			editorSettings,
			onToggle,
		} = this.props;

		const closeModal = () => (
			this.setState( { isOpen: false } )
		);

		return (
			<Fragment>
				<PluginMoreMenuItem
					icon={ null }
					role="menuitemcheckbox"
					onClick={ () => {
						this.setState( { isOpen: true } );
					} }
				>
					{ __( 'EditorsKit Settings', 'block-options' ) }
				</PluginMoreMenuItem>
				{ this.state.isOpen ?
					<Modal
						title={ __( 'EditorsKit Features Manager', 'block-options' ) }
						onRequestClose={ () => closeModal() }
						closeLabel={ __( 'Close', 'block-options' ) }
						icon={ null }
						className="editorskit-modal-component components-modal--editorskit-features-manager"
					>
						{ map( editorSettings.editorskit, ( category ) => {
							return (
								<section className="edit-post-options-modal__section">
									<h2 className="edit-post-options-modal__section-title">{ category.label }</h2>
									<ul className="edit-post-editorskit-manager-modal__checklist">
										{ map( category.items, ( item ) => {
											return (
												<li
													className="edit-post-editorskit-manager-modal__checklist-item"
												>
													<CheckboxControl
														className="edit-post-options-modal__option"
														label={ item.label }
														checked={ ! select( 'core/edit-post' ).isFeatureActive( 'disableEditorsKit' + capitalize( item.name ) + capitalize( category.name ) ) }
														onChange={ () => onToggle( category.name, item.name ) }
													/>
												</li>
											);
										} ) }
									</ul>
								</section>
							);
						} ) }
					</Modal> :
					null }
			</Fragment>
		);
	}
}

export default compose( [
	withSelect( () => ( {
		editorSettings: select( 'core/editor' ).getEditorSettings(),
		preferences: select( 'core/edit-post' ).getPreferences(),
	} ) ),
	withDispatch( ( dispatch ) => ( {
		onToggle( category, item ) {
			dispatch( 'core/edit-post' ).toggleFeature( 'disableEditorsKit' + capitalize( item ) + capitalize( category ) );
		},
	} ) ),
	withSpokenMessages,
] )( FeaturesManager );
