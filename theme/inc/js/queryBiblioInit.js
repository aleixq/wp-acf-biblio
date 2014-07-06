function queryBiblioInit(){
	jQuery(document).ready(function($){
		/*
		* NOTE:
		* This file is part of a package provided to filter data in a biblioteca. Refer to biblio_backend plugin README
		*/
		/*
		* Show only Items of class [bilbiotype_class], show title spec by [title], 
		* and Show only filterset in specific [context]
		*/
		function showOnly(context, bibliotype, title){
			//Restrict the list to a given type of biblioteca files.
			//console.log("hide all but ."+bibliotype);
			setTitle(title)
			$(".filterset").hide()
			$(".filterset",context).show()
			getDataBibliotype(bibliotype)
		}
		/*
		* Sets page title
		*/
		function setTitle(title){
			$("h2#biblioTitle").text(title)
		}
		/*
		* Returns selected data for [bibliotype]
		*/
		function getDataBibliotype(bibliotype){
			year=$( "select#year[data-bibliotype='"+bibliotype+"']").find(":selected").text();
			tema=$( "select#tema[data-bibliotype='"+bibliotype+"']").find(":selected").text();
			//console.log("querying {'bibliotype':'"+bibliotype+"', 'year':'"+year+"','tema':'"+tema+"'}" )
			/* Ajax call*/
			$(".centro").html("cargando...")
			jQuery.getJSON(
				BiblioBackend.ajaxurl,
				{
					tema: tema,
					year: year,
					bibliotype: bibliotype,
					action: 'query-biblioteca',
					nonce: BiblioBackend.nonce
				},
				function( response ) {
					//console.log( response.success );
					//console.log( response.results );
					$(".centro").html(response.pretty_results);
					
				}
			);
		}
		/*
		* Starts with all data selected
		*/
		function startBiblio(){
			$(".centro").html("cargando...")
			jQuery.getJSON(
				BiblioBackend.ajaxurl,
				{
					tema: "todos",
					year: "todos",
					bibliotype: "todos",
					action: 'query-biblioteca',
					nonce: BiblioBackend.nonce
				},
				function( response ) {
					//console.log( response.success );
					//console.log( response.results );
					$(".centro").html(response.pretty_results);
					
				}
			);
		}
		/*
		* React on click each biblotype menu list item
		*/
		$("#tpdfs").click(function(){
			showOnly($(this).parent(),"pdfs", "Pdf's");//$("li.biblioitem").not(".pdfs").hide()
		});
		$("#tdocs").click(function(){
			showOnly($(this).parent(),"docs","Documentos"); 
		}); 
		$("#tslides").click(function(){ 
			showOnly($(this).parent(),"slides","Presentaciones");
		}); 
		$("#tvideos").click(function(){ 
			showOnly($(this).parent(),"videos","Vídeos")
		}); 
		/*
		* Restrict to only the selected items whenever option in select is choosen
		*/
		
		$( ".filterset select.bibliotypes" )
		.change(function () {		
			//console.log("changing options");
			bibliotype=$(this).data("bibliotype");
			getDataBibliotype(bibliotype);
		})
		
		//Init with all data:
		startBiblio();
	});
}

