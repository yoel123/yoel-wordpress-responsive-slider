<?php 
    /*
    Plugin Name: yoel_slider
    Plugin URI: 
    Description: Plugin test
    Author: yoel rosfisher
    Version: 1.0
    Author URI:
    */
	
	include( 'cuztom/cuztom.php' );
	///500 error becuse its not on init sanitize_categoryaction add_action
	//add_action('init', array( $this, 'register_post_type' ) ); to
	//post type class constructor
	
	
/*
usge


*/



////////slider///////////
//postype
$slider = new Cuztom_Post_Type( 'yoel_slider', array(
    'has_archive' => true,
    'supports' => array( 'title' )
	) );
	
	$slider->add_meta_box(
        'img_slids',
    'slider content',
    array(
            'bundle', 
        array(
            array(
                'name'          => 'img_title',
                'label'         => 'img title',
                'description'   => 'your slide name',
                'type'          => 'text'
            ),
            array(
						'name'          => 'img_slider',
						'label'         => 'Image slide',
						'description'   => 'select an img for this slide',
						'type'          => 'image',
					)
        )
    )
	);//end bundle
	
//shortcodes
//enqu scripts

///slider output and shortcodes

///shortcodes
//[yslider name="slidertst"]
function yslider_shortcode($atts) 
{
   extract(shortcode_atts(array(
      'width' => 800,
	  'name' => "slidertst",
      'height' => 330,
	  'type' => "norm",
   ), $atts));
	return yget_slider($name,$width,$height,$type);
}//end yslider_shortcode

add_shortcode('yslider', 'yslider_shortcode');

function yget_slider($name,$width = 800,$height=200,$type = "nav")
{
	//get slider post id by name
	$post = yget_post_by_title($name,"yoel_slider");
	//the sliders post id
	$id =$post->ID;
	//print_r(get_post_meta($post->ID, '_img_slids', true));
	//get imgs ids as array
	$imgs =  get_post_meta($post->ID, '_img_slids', true);

	//container
	$html .="<div class='yslider_container'>";
	//the ul tag <ul clas=blabla>
	$html .= yul_slider_open_tag($name,$width,$type);
	
	foreach( $imgs as $img)
	{
		//single slide
		$html .= single_slide($img,$height);
	}
	$html .= "</ul>";
	if($type == "img_tabs")
	{
		$html .= yslider_pager($imgs,$name);
	}
	//end container
	$html .="</div>";
	yslider_fotter_js($name,$type) ;//echo js
	return $html;//echo slider
	
	
	
}//end yget_slider

//creats the img tabs below the slider if its img_tabs slider
//$imgs = array of img ids
function yslider_pager($imgs,$name)
{
	$html = '<ul id="'.$name.'slider-pager" class ="slider-pager">';
	foreach( $imgs as $img)
	{
		//get the img id
		$img_id = $img["_img_slider"];
		$html .= "<li><a href='#'>";
		//get the img url
		$img_url = wp_get_attachment_image_src($img_id);
		//$img_id .=wp_get_attachment_image($img_id);
		$html .= '<img src="'.$img_url[0].'"  />';
		$html .= "</a></li>";
	}
	$html .= "</ul>";
	return $html;
}

//slider js

//js and php logic fun
function yslider_fotter_js($id,$type) 
{
	$html = '<script>

    $( document ).ready(function () {
	';
	if($type == "norm")//php if
	{
		
		$html .= '// Slideshow 1
      jQuery("#'.$id.'").responsiveSlides({
       // maxwidth: 800,
        speed: 800
      });//end norm';
	  
	}//end norm
	
	if($type == "nav")//php if
	{
		
	  $html .= '// Slideshow 4
      $("#'.$id.'").responsiveSlides({
        pager: false,
		nav: true,
		pager: false,
		speed: 800
	  });';
	  
	}//end nav
	if($type == "img_tabs")//php if
	{  
	
	   $html .= ' // Slideshow 3
      $("#'.$id.'").responsiveSlides({
        manualControls: "#'.$id.'slider-pager",
       // maxwidth: 800
      });';
	  
	}//end img_tabs
	//end script
	$html .= '
	$("#'.$id.'").parent().width($("#'.$id.'").width());
	});
	  </script>';
	  echo $html;
}//end yslider_fotter_js


////////render helper funcs (genrate html or js)//////////

function yul_slider_open_tag($name,$width,$type)
{
	
	if($type == "norm")
	{
		$html = '<ul class="rslides norm" id="'.$name.'" style="max-width:'.$width.'px;">';
	}
	if($type == "nav")
	{
		$html = '<ul class="rslides nav" id="'.$name.'" style="max-width:'.$width.'px;">';
		
	}
	
	if($type == "img_tabs")
	{
		$html = '<ul class="rslides img_tabs" id="'.$name.'" style="max-width:'.$width.'px;">';
		
	}
	
	return $html;
	
}//yul_slider_open_tag

function single_slide($img,$height)
{
		$html = "";
		//get the img id
		$img_id = $img["_img_slider"];
		$html .= "<li>";
		//get the img url
		$img_url = wp_get_attachment_image_src($img_id);
		//$img_id .=wp_get_attachment_image($img_id);
		$html .= '<img src="'.$img_url[0].'" style="height: '.$height.'px;" />';
		if(isset($img["_img_title"]))
		{
				$html .='<p class="caption">'.$img["_img_title"].'</p>';
		}
		$html .= "</li>";
		return $html;
}//end single_slide



///end slider output and shortcodes



//jqury (also makes sure no conflicts)
 add_action("wp_enqueue_scripts", "my_jquery_enqueue", 11);
function my_jquery_enqueue() {
   wp_deregister_script('jquery');
   wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js", false, null);
   wp_enqueue_script('jquery');
}

//enque other scripts

function ywp_adding_scripts()
{
	//responsive slider js
	wp_register_script('responsiveslides_sctipt_', plugins_url('js/responsiveslides.min.js', __FILE__), array('jquery'),'1.1', true);
	wp_enqueue_script('responsiveslides_sctipt_');
}

add_action( 'wp_enqueue_scripts', 'ywp_adding_scripts' );  

////encue style
function ywp_adding_styles() 
{

	//responsive slider css
	wp_enqueue_style('responsiveslides_css', plugins_url('css/responsiveslides.css', __FILE__));
	//wp_enqueue_script('responsiveslides_css');
}

add_action( 'wp_enqueue_scripts', 'ywp_adding_styles' ); 

////permissions
add_action( 'in_admin_header', function()
{
	//only admin can edit
	if (!current_user_can('activate_plugins') && $_GET['post_type']=="yoel_slider"){
        /*do something*/
			//return;
			
			exit("<h1>you dont have permission to use this page</h1>");
	}
} ); 


////add help page
function yadd_submenus_pages()
{
	add_submenu_page(
		'edit.php?post_type=yoel_slider',
		'how to use', /*page title*/
		'how to use', /*menu title*/
		'manage_options', /*roles and capabiliyt needed*/
		'wnm_fund_set',
		'yhelp_page' /*replace with your own function*/
	);
}
add_action( 'admin_menu', 'yadd_submenus_pages' );

function yhelp_page()
{
	//chack user level_10
	if (current_user_can('level_10')){
        /*do something*/
		//	return;
	}
	echo '<div class="wrap"><h2>how to use</h2></div>';
	//$src = plugin_dir_path( __FILE__ ."help.swf");
	$src =  plugins_url( 'help.swf' , __FILE__ );
	//vidio link
	echo '<a href="'.$src.'">video tutorial</a>';
	//example shortodes
	echo "<h2>example shortcodes</h2>";
	echo '<input type="text" value=\'[yslider name="slider_name"]\' size="33" style="
    direction: ltr;
"/></br></br>';
	echo '<input type="text" value=\'[yslider name="slider_name" type="nav"]\'size="39" style="
    direction: ltr;
"/></br></br>';
	echo '<input type="text" value=\'[yslider name="slider_name" type="img_tabs"]\'size="49" style="
    direction: ltr;
" /></br></br>';
	echo '<input type="text" value=\'[yslider name="slider_name" type="nav" width="400" height="400"]\'size="69	" style="
    direction: ltr;
"/></br></br>';

	
}

////////end slider///////////


////////castum colloums/////////////////////
$postype = "yoel_slider";

///////add colums////////
add_filter( 'manage_edit-'.$postype.'_columns',  
function ( $columns ) {
	//cullom names
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'title' ),
		"shortcode" =>__( 'shortcode' )
		
		//
		//'title' => __( 'Movie' ),
		//'duration' => __( 'Duration' ),
		//'genre' => __( 'Genre' ),
		//'date' => __( 'Date' )
	);

	return $columns;
});

///add collum data///
add_action( 'manage_'.$postype.'_posts_custom_column', 

function ( $column, $post_id ) 
{
	//collums content
	global $post;
	/* If displaying the 'name_date' column. */
	if( $column == "shortcode")
	{

		//[yslider name="slider_name"]
		echo "<input type='text' size='33' value='[yslider name=\"".$post->post_title."\"]'  style='
				direction: ltr;
			'/>"  ;

	}
}, 10, 2 );


////////end castum colloums/////////////////////


////helper funcs/////
function yget_post_by_title($page_title,$postype, $output = OBJECT) {
    global $wpdb;
        $post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='".$postype."'", $page_title ));
        if ( $post )
            return get_post($post, $output);

    return null;
}

?>