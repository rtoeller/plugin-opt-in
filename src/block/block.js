/**
 * BLOCK: single-block
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import './style.scss';
import './editor.scss';

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

const {
    RichText,
    InspectorControls,
    ColorPalette
} = wp.editor;


/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType( 'cgb/block-single-block', {
	// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __( 'OptIn Content', 'Rebeca Töller' ), // Block title.
	icon: 'smiley', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'widgets', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		__( 'Mein Gutenberg' ),
		__( 'CGB Example' ),
		__( 'create-guten-block' ),
	],

	// The "edit" property must be a valid function.
	edit: function( props ) {
		// Creates a <p class='wp-block-cgb-block-single-block'></p>.
		return (
			<div className={ props.className }>
				<p>— Hello from the backend.backend.backend.</p>
				<p>
					CGB BLOCK: <code>single-block</code> is a new Gutenberg block
				</p>
				<p>
					It was created via{ ' ' }
					<code>
						<a href="https://github.com/ahmadawais/create-guten-block">
							create-guten-block
						</a>
					</code>.
				</p>
			</div>
		);
	},

	// The "save" property must be specified and must be a valid function.
	save: function( props ) {
		return (
            <div id="overlay1" className="my-overlay" style="background-color: #8224e3;height: 450px;width: 600px;">
                <div className="my-overlay-inner">
                    <img width="213" height="300"
                         src="https://rtoell.macskay.com/wp-content/uploads/2019/01/photo5282873821088557705-213x300.jpg"
                         className="attachment-medium size-medium" alt=""
                         srcSet="https://rtoell.macskay.com/wp-content/uploads/2019/01/photo5282873821088557705-213x300.jpg 213w, https://rtoell.macskay.com/wp-content/uploads/2019/01/photo5282873821088557705.jpg 638w"
						 sizes="(max-width: 213px) 100vw, 213px"></img>
					<input type="hidden" value="1" name="overlay_content_id"></input>
                            <div className="overlayText">

                            </div>
                            <button className="button laden" value="1">Klick</button>

                </div>
            </div>
    );
	},
} );
