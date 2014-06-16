<?php
/**
 * @package biblio_backend
 * @version 0.1
 */
/*
Plugin Name: biblioteca backend
Plugin URI: http://communia.org
Description: A simple biblioteca navigating plugin
Version: 0.1
Author: Aleix Quintana
Author URI: http://communia.org/
*/
// kudos to:
//	*http://stv.whtly.com/2011/09/12/using-front-end-ajax-requests-in-your-wordpress-plugins/
//	*http://stackoverflow.com/questions/18304269/get-post-data-using-ajax

class BiblioBackend
{
	public function __construct()
	{
			if ( is_admin() ) {
				add_action( 'wp_ajax_nopriv_query-biblioteca', array( &$this, 'query_bibliobackend' ) );
				add_action( 'wp_ajax_query-biblioteca', array( &$this, 'query_bibliobackend' ) );
			}
			add_action( 'init', array( &$this, 'init' ) );
		
		
	}

	public function init()
	{
		//create an ajax call! (via jquery)
		wp_enqueue_script( 'biblio_backend', plugin_dir_url( __FILE__ ) . 'biblio_backend.js', array( 'jquery' ) );
		wp_localize_script( 'biblio_backend', 'BiblioBackend', array(
		    'ajaxurl' => admin_url( 'admin-ajax.php' ),
		    'nonce' => wp_create_nonce( 'biblio-backend-nonce' )
		) );
	}
	/*
	* Filter array looking for specific $params. (taken from http://stackoverflow.com/questions/21319729/filter-sort-multidimensional-array-with-multiple-values) 
	*  print_r(filter($allrows,array("tipo_documento" => "Pdf's", "year" => "04/06/2014"))   );

	*/
	private function filter_biblioitems(array $arr,array $params){
		//remove todos (all) from params
		$params=array_filter($params, function ($element) { return ($element != "todos"); } ); 
		$out = array();
		foreach($arr as $key=>$item){
			$diff = array_diff_assoc($item,$params);
			if (count($diff)==count($item)-count($params))
				$out[$key] = $item;
		}
		return $out;
	}
	/*
	* Themes the fields depending the type of document. All fields are available:
	* Normally:
	*			$fields[titulo] 
	*			$fields[tipo_documento] 
	*			$fields[year] 
	*			$fields[tema] 
	*			$fields[descripcion] 
	*			$fields[upload] 
	*
	*	
	*/
	private function theme_fields($fields){
		
		switch ($fields['tipo_documento']){
			case "Pdf's":
				$icon="book";
				$kind="pdfs";
				break;
			case "Presentaciones":
				$icon="clipboard";
				$kind="slides";
				break;
			case "Documentos":
				$icon="files-o";	
				$kind="docs";
				break;
			case "Vídeos":
				$icon="video-camera";
				$kind="videos";
				break;
			default:
				;
		}
		//variable containing field printable array, choose what you need
		$fields_data=print_r($fields,true);
		//ITEM MARKUP
		return "
		<li class='biblioitem $kind'>
			<div class='titulo'><i class='fa fa-$icon '> </i>$fields[titulo]</div>
			<div class='descripcion'>$fields[descripcion]<div>
			<div class='download'><a href='$fields[upload]'>descargar</a></div>
			<small><div style='display:none;'><pre>$fields_data</pre></div></small>
		</li>";
	}	
	public function query_bibliobackend()
	{
		//Define and filter the conditional query taking the GET vars
		$_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
		$short_bibliotype=(isset($_GET['bibliotype'])?$_GET['bibliotype']:"todos");
		$year=(isset($_GET['year'])?$_GET['year']:"todos");
		$tema=(isset($_GET['tema'])?$_GET['tema']:"todos");
		$bibliotypes=array(
			'pdfs'=>"Pdf's",
			"slides"=>"Presentaciones",
			"docs"=>"Documentos",
			"videos"=>"Vídeos",
			"todos" => "todos",
		);
		$bibliotype=$bibliotypes[$short_bibliotype];

		//ACF load results
		$other_page = 639;
		
 		$biblioitems=Array();
 		$results=Array();
		if( have_rows('biblioteca',$other_page)):
			while ( have_rows('biblioteca',$other_page) ) : the_row();
				$layout_selected=get_row_layout();
				switch ($layout_selected){
					case 'archivos':
						$fields=Array(
							'titulo'=>get_sub_field('titulo'),
							'tipo_documento'=>get_sub_field('tipo_documento'), 
							'year'=>get_sub_field('year'), 
							'tema'=>get_sub_field('tema'), 
							'descripcion'=>get_sub_field('descripcion'), 
							'upload'=>get_sub_field('upload')
						);
						array_push($biblioitems,$fields);
						break;
					default:
					;
				}
 
			endwhile;
			//Filter results according to Get Data.
			$results=$this->filter_biblioitems($biblioitems,array("tipo_documento"=>$bibliotype,"year"=>$year,"tema"=>$tema));
			$pretty_results=array_map(array($this,"theme_fields"),$results);
		else :
			// no layouts found
		
		endif;
		
				
				
		if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'biblio-backend-nonce' ) )
			die ( 'Invalid Nonce' );
		header( "Content-Type: application/json" );
		echo json_encode( array(	
			'title' => array("tipo_documento"=>$bibliotype,"year"=>$year,"tema"=>$tema),
			//'all_items' => $biblioitems,
			'results' => $results,
			'pretty_results' => implode("",$pretty_results),
			'success' => true,
			'time' => time()
		) );
		exit;
	}

}
$biblio_backend = new BiblioBackend();

?>