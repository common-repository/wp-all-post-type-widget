<?php

class WPAPT_Widget extends WP_Widget{

	public function __construct(){
		$widget_ops = array( 'classname' => 'wpaptw_posts_entries', 'description' => __( 'Your site’s custom Posts.', 'wpaptw-all-post-type-widget' ) );
		parent::__construct( 'wpaptw-all-post-type-widget-posts', __( 'WP All Post Type Widget', 'wpaptw-all-post-type-widget' ), $widget_ops );
		$this->alt_option_name = 'widget_wpaptw_all_post_type_widget_posts';
		add_action( 'wp_head', array($this,'public_scripts'));
		add_action( 'admin_enqueue_scripts', array($this,'scripts'));
		add_action('wp_ajax_wpaptw_get_category',array($this,'wpaptw_get_category'));
        add_action('wp_ajax_nopriv_wpaptw_get_category',array($this,'wpaptw_get_category')); 
	}

	public function widget($args,$instance){
		$post_types=$this->get_all_post_types();

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Recent Posts', 'custom-post-type-widgets' ) : $instance['title'], $instance, $this->id_base );

		$posttype = ! empty( $instance['posttype'] ) ? $instance['posttype'] : 'post';
		
		if ( empty( $instance['number_of_post'] ) || ! $number = absint( $instance['number_of_post'] ) ) {
			$number = 5;
		}
		$show_post_date = isset( $instance['show_post_date'] ) ? $instance['show_post_date'] : false;
		
		$category=isset( $instance['category'] ) ? $instance['category']: '';
		$orderby=isset( $instance['orderby'] ) ? $instance['orderby']: 'date';
		$order=isset( $instance['order'] ) ? $instance['order']: 'desc';
		
		if ( array_key_exists( $posttype, (array) $post_types ) ) {
			$post_args=array(
				'post_type' => $posttype,
				'posts_per_page' => $number,
				'no_found_rows' => true,
				'post_status' => 'publish',
				'ignore_sticky_posts' => true,
				'orderby' => $orderby,
				'order' => $order,

			);

			if(!empty($category) && $category!="all"){
				$category_part=explode(':', $category);
				$post_args['tax_query']=array(
		            array(
		                'taxonomy' => $category_part['0'],
		                'field' => 'id',
		                'terms' => $category_part[1]
		            )
		        );
			}

			$r = new WP_Query( apply_filters( 'widget_posts_args', $post_args ) );

			if ( $r->have_posts() ) : ?>
				<?php echo $args['before_widget']; ?>
				<?php if ( $title ) {
					echo $args['before_title'] . $title . $args['after_title'];
				} ?>
				<ul class="wpaptw-posts-ul">
				<?php while ( $r->have_posts() ) : $r->the_post(); ?>
					<li>
						<?php if(has_post_thumbnail()){?>
							<div class="wpaptw-thumbnail"><?php the_post_thumbnail(array(75,75));?></div>
						<?php }?>
						<div <?php if(has_post_thumbnail()){ ?> class="half_title" <?php } ?>>
							<a href="<?php the_permalink() ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a>
							<?php if ( $show_post_date ) : ?>
								<span class="post-date"><?php echo get_the_date(); ?></span>
							<?php endif; ?>
						</div>
					</li>
				<?php endwhile; ?>
				</ul>
				<?php echo $args['after_widget']; ?>
				<?php
				wp_reset_postdata();
			endif;
		}
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['posttype'] = strip_tags( $new_instance['posttype']);
		$instance['orderby'] = strip_tags( $new_instance['orderby']);
		$instance['order'] = strip_tags( $new_instance['order']);
		$instance['category'] = strip_tags( $new_instance['category']);
		$instance['number_of_post'] = (int) $new_instance['number_of_post'];
		$instance['show_post_date'] = isset( $new_instance['show_post_date'] ) ? (bool) $new_instance['show_post_date'] : false;
		return $instance;
	}

	public function form($instance){
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$posttype = isset( $instance['posttype'] ) ? $instance['posttype']: 'post';
		$post_types=$this->get_all_post_types();
		$category=isset( $instance['category'] ) ? $instance['category']: 'all';
		$orderby=isset( $instance['orderby'] ) ? $instance['orderby']: 'date';
		$order=isset( $instance['order'] ) ? $instance['order']: 'desc';
		$number_of_post= isset( $instance['number_of_post'] ) ? esc_attr( $instance['number_of_post'] ) : 5;
		$show_post_date = isset( $instance['show_post_date'] ) ? (bool) $instance['show_post_date'] : false;
		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' );?>"><?php _e('Title:',WPAPTW_NAME);?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" type="text" value="<?php echo $title; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('posttype');?>"><?php _e( 'Post Type:', WPAPTW_NAME);?></label>
				<select class="widefat wpaptw_post_types_box" id="<?php echo $this->get_field_id('posttype');?>" name="<?php echo $this->get_field_name('posttype');?>">
					<?php if($post_types && count($post_types)>0){?>
						<?php foreach ($post_types as $key => $value) {?>
							<option value="<?php echo $key;?>" <?php selected($posttype,$key,true); ?>><?php echo $value;?></option>
						<?php }?>
					<?php }?>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('category');?>"><?php _e( 'Category:', WPAPTW_NAME);?></label>
				<select id="<?php echo $this->get_field_id('category');?>" name="<?php echo $this->get_field_name('category');?>" class="widefat wpaptw_category_combo">
				<?php echo $this->create_option($posttype,$category);?>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('orderby');?>"><?php _e( 'Orderby:', WPAPTW_NAME);?></label>
				<select id="<?php echo $this->get_field_id('orderby');?>" name="<?php echo $this->get_field_name('orderby');?>" class="widefat wpaptw_orderby_combo">
					
					<option value="title" <?php selected($orderby,'title',true); ?>>Title</option>
					<option value="date" <?php selected($orderby,'date',true); ?>>Date</option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('order');?>"><?php _e( 'Order:', WPAPTW_NAME);?></label>
				<select id="<?php echo $this->get_field_id('order');?>" name="<?php echo $this->get_field_name('order');?>" class="widefat wpaptw_order_combo">
					
					<option value="asc" <?php selected($order,'asc',true); ?>>ASC</option>
					<option value="desc" <?php selected($order,'desc',true); ?>>DESC</option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'number_of_post' );?>"><?php _e('Number of posts to show:',WPAPTW_NAME);?></label>
				<input class="tiny-text" id="<?php echo $this->get_field_id('number_of_post');?>" name="<?php echo $this->get_field_name('number_of_post');?>" type="number" step="1" min="1" size="3" value="<?php echo $number_of_post; ?>" />
			</p>
			<p>
				<input class="checkbox" type="checkbox" <?php checked( $show_post_date ); ?> id="<?php echo $this->get_field_id( 'show_post_date' ); ?>" name="<?php echo $this->get_field_name( 'show_post_date' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'show_post_date' ); ?>"><?php _e( 'Display post date?', WPAPTW_NAME ); ?></label></p>
		<?php
		
	}

	public function wpaptw_get_category(){
		$html='';
		if(isset($_GET['post_type']) && !empty($_GET['post_type'])){
			$post_type=esc_attr($_GET['post_type']);
			$html.=$this->create_option($post_type);
		}
		echo $html;
		exit;
	}

	private function create_option($post_type,$selected=""){
		$html='<option value=""all">All</option>';
		$categories=$this->get_post_type_wise_taxonomies($post_type);
		if($categories && count($categories)>0){
			foreach ($categories as $key => $category) {
				if(count($category)>0){
					foreach ($category as $cat) {
						if(count($cat)>0){
							$id=$key.":".$cat->term_id;
							$html.='<option value="'.$id.'" '.selected($selected,$id,false).'>'.$cat->name.'</option>';	
						}
						
					}
				}
			}
		}
		return $html;	}

	private function get_all_post_types(){
		$post_types = get_post_types( array( 'public' => true ), 'objects');
		$posttypes=array();
		if(count($post_types)>0){
			foreach ( $post_types as $post_type => $value ) {
				if ( 'attachment' === $post_type || 'page'===$post_type  || 'product'===$post_type) {
					continue;
				}

				$posttypes[esc_attr( $post_type )]=$value->label;
			}
		}
		return $posttypes;
	}

	private function get_post_type_wise_taxonomies($post_type){
		$taxonomies=get_object_taxonomies($post_type,'objects');
		$categories=array();
		if ($taxonomies) {
			foreach ( $taxonomies as $taxobjects => $value ) {
				if ( ! $value->hierarchical ) {
					continue;
				}
				if ( 'nav_menu' === $taxobjects || 'link_category' === $taxobjects || 'post_format' === $taxobjects ) {
					continue;
				}
				
				$terms = get_terms( array(
				    'taxonomy' =>$value->name,
				    'hide_empty' => false,
				));
				if($terms && count($terms)>0){
					$categories[$value->name]=$terms;	
				}
				
			}
		}
		return $categories;
	}

	public function public_scripts(){
		wp_enqueue_style('wpaptw-style',WPAPTW_ASSETS_URL.'css/wpaptw.css');
	}

	public function scripts(){
		wp_enqueue_script('wpaptw-script',WPAPTW_ASSETS_URL.'js/wpaptw.script.js',array('jquery'),null,true);
	}

}