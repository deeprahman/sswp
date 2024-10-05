import "./js/wpss-file-permissions-request.js";

import domReady from '@wordpress/dom-ready';
import { createRoot } from '@wordpress/element';

const ToolsPage = () => {
    return <div>Placeholder for settings page</div>;
};

domReady( () => {
    const root = createRoot(
        document.getElementById( 'unadorned-announcement-bar-settings' )
    );

    root.render( <ToolsPage /> );
} );