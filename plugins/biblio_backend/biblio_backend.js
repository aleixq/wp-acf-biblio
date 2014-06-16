jQuery.getJSON(
    BiblioBackend.ajaxurl,
    {
        action: 'query-biblioteca',
        nonce: BiblioBackend.nonce
    },
    function( response ) {
    	//console.log( response.success );
		//console.log( response );
    }
);