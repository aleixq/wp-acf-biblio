<?php
/**
	 * Template Name: Biblioteca
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that other
 * 'pages' on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
get_header(); 
//Note:  Do not include the opening and closing PHP tags when you copy this code

/*NOTE:
* This file is part of a package provided to filter data in a biblioteca. Refer to biblio_backend plugin README
* This template creates a view to query specific fields created with ACF: Separating content based on tipo_documento, then you can filter by fecha and by tema. It loads a js queryBiblioInit.js.
* The main function of this template is to create query constructor menu (left), and needs the plugin biblio_backend to work as it provides the ajax endpoint to ask querys
* As a todo if things go big or if implemented other places, refer to biblio_backend plugin TODO notes.
*/
?>


<?php
/*
* Filter array looking for specific $params. (taken from http://stackoverflow.com/questions/21319729/filter-sort-multidimensional-array-with-multiple-values) 
*  print_r(filter($allrows,array("tipo_documento" => "Pdf's", "year" => "04/06/2014"))   ); Used in menu filterset. 

*/
function filter_array(array $arr,array $params){
  $out = array();
  foreach($arr as $key=>$item){
     $diff = array_diff_assoc($item,$params);
     if (count($diff)==count($item)-count($params))
        $out[$key] = $item;
 }
 return $out;
}

/*
* Prints the dropdowns filter selects set foreach facet
*/
function filter_set(array $arr, $bibliotype){
  print "<div id='$bibliotype' class='filterset'>";
  $selects=array("year"=>"fecha","tema"=>"tema");
  foreach ($selects as $select_type=>$localized_select_type){
    print "$localized_select_type :";
    print "<select id='$select_type' class='dropdown bibliotypes' name='$select_type' data-bibliotype='$bibliotype'>";
    print '<option selected="true">todos</option>';
    foreach(    array_unique( array_column($arr,$select_type) )   as $i   ){
      print "<option>$i</option>";
    }
    print "</select>";
  }
  print "</div>";

}

?>


<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/../sarqua/inc/js/queryBiblioInit.js"></script>
<script type="text/javascript">
<!--
queryBiblioInit();
//--></script>
<style>
.biblioitem{
	display:block;
}
.filterset{
	display:none;
}
</style>

<div id="primary" class="content-area">
<div id="content" class="site-content" role="main">
<header class="image-header">

<?php if ( has_post_thumbnail() && ! post_password_required() && ! is_attachment() ) : ?>
	<?php $url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );?>
	<div class="entry-header-image" style="background-image: url(<?php echo $url; ?>);">

	</div>
		<?php endif; ?>
</header><!-- .entry-header -->
	<?php if ( is_search() ) : // Only display Excerpts for Search ?>
<div class="entry-summary">
	<?php the_excerpt(); ?>
</div><!-- .entry-summary -->
	<?php else : ?>

<article id="post-<?php the_ID(); ?>" class="microcentro he-sar-114 clearfix">
	<a name="residencia"></a>
	<header>
		<div class="col-one-fourth"></div>
		<div class="entry-content-three-fourth">
			<?php if ( is_single() ) : ?>
			<h1 class="entry-title-44"><?php the_title(); ?></h1>
			<?php else : ?>
			<h1 class="entry-title-44">
				<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
			<?php endif; // is_single() ?>
		        <h2 id="biblioTitle"></h2>
		</div>
		<div class="clear"></div>
	</header>
	
	
	


<?php
//CREATE MENU LIST FILTERS - FILTERSET
if( have_rows('biblioteca') ):
  $allrows=array();
  while ( have_rows('biblioteca') ) : the_row();
    $item=Array(
				'titulo'=>get_sub_field('titulo'),
				'tipo_documento'=>get_sub_field('tipo_documento'), 
				'year'=>get_sub_field('year'), 
				'tema'=>get_sub_field('tema'), 
				'descripcion'=>get_sub_field('descripcion'), 
				'upload'=>get_sub_field('upload')
			);
    array_push($allrows,$item);
  endwhile;
endif;
$pdfs=filter_array($allrows,array("tipo_documento" => "Pdf's"));
$docs=filter_array($allrows,array("tipo_documento" => "Documentos"));
$videos=filter_array($allrows,array("tipo_documento" => "Vídeos"));
$slides=filter_array($allrows,array("tipo_documento" => "Presentaciones"));


?>
	
	<div class="col-one-fourth sticky-menu">
		<div class="menu-list">
			<ul>
<?php 
//MENU - LIST
?>			
				<li><div id="tpdfs" class="biblioType"><i class="fa fa-book"></i> Pdf's pdfs</div>
				<?php filter_set($pdfs,"pdfs");?></li>
				<li><div id="tslides" class="biblioType"><i class="fa fa-clipboard"></i> Presentaciones</div>
				<?php filter_set($slides,"slides");?></li>
				<li><div id="tdocs" class="biblioType" ><i class="fa fa-files-o"></i> Documentos</div>
				<?php filter_set($docs,"docs");?></li>
				<li><div id="tvideos" class="biblioType"><i class="fa fa-video-camera"></i> Vídeos</div>
				<?php filter_set($videos,"videos");?></li>
			</ul>
		</div>

	</div>
	<div class="entry-content-three-fourth">
		<section class="centro">
			<div class="sub-title">
			<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentythirteen' ) ); ?>
			</div>



<?php

// GETTING FIRST DATA WITHOUT AJAX 
/*
//FIRST SHOW ALL ITEMS
// check if the flexible content field has rows of data
if( have_rows('biblioteca') ):
     //print "This is the flexible content with rows of data";
     //WRAPPER OF ITEMS
     print "<ul>";
     // loop through the rows of data
     while ( have_rows('biblioteca') ) : the_row();
        //print "This is the container :loops rows<br/>"; //http://www.advancedcustomfields.com/resources/getting-started/displaying-custom-field-values-in-your-theme/
        $layout_selected=get_row_layout();
		//Switch depending the layout
        switch ($layout_selected){
		case 'archivos':
			//We have table of Archivos layout selected
			$fields=Array(
				'titulo'=>get_sub_field('titulo'),
				'tipo_documento'=>get_sub_field('tipo_documento'), 
				'year'=>get_sub_field('year'), 
				'tema'=>get_sub_field('tema'), 
				'descripcion'=>get_sub_field('descripcion'), 
				'upload'=>get_sub_field('upload')
			);
			theme_fields($fields);
			break;
					
	      default:
			;
	}
 
    endwhile;
    print "</ul>";
else :
 
    // no layouts found
 
endif;
 */
?>





































		</section><!-- .centro -->
		
	</div><!-- .entry-content-three-fourth -->
	<?php endif; ?>



</article><!-- #post -->
<div class="clear"></div>
		</div><!-- #content -->
	</div><!-- #primary -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
