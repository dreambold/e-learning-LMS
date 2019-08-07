<?php

class EIContent_Frontend {

	/**
	* @var object Refer to global $wpdb
	*/
	private $_db;

	/**
	* @var object EIContent_Model instance
	*/
	private $_model;

	/**
	* Set _db and _model properties.
	* Activate activation and unactivation hooks.
	* Choose to trigger Site Front End or Back End hooks
	*
	* @return void
	*/
	public function __construct() {
		$this->_db = &$GLOBALS['wpdb'];
		$this->_model = new EIContent_Model($this->_db);
	}

	/**
	* Exclude Pages from show to End User accordance to
	* unchecked Exclude Include Content plugin checkboxes.
	* By filtering of the Pages objects array.
	*
	* @param array of the Pages objects
	* @return array of the Pages objects filtered
	*/
	public function exclude_pages($pages) {
		$query = "SELECT `ID` FROM `" . $this->_db->posts . "` WHERE `" . $this->_model->get_element_column() . "` = 0 AND `post_type` = 'page';";
		$this->_model->set_excluded_ids( $this->_db->get_col( $query ) );

		return array_filter( $pages, array( $this->_model, 'exclude_posts_ids' ) );
	}

	/**
	* Exclude Posts from show to End User accordance to
	* unchecked Exclude Include Content plugin checkboxes.
	* By setting category__not_in and post_tag__not_in elements of the uery_vars array
	* (an array of the query variables and their respective values).
	*
	* @param object of the Pages objects
	* @return object of the Pages objects filtered
	*/
	public function exclude_posts($query_request) {
		$query_vars   = isset($query_request->query_vars)  ? $query_request->query_vars  : array();
		$post__not_in = isset($query_vars['post__not_in']) ? $query_vars['post__not_in'] : array();

		if ( isset($query_vars['post_type']) && $query_vars['post_type'] === 'page' ) {
			$query = "SELECT `ID` FROM `" . $this->_db->posts . "` WHERE `" . $this->_model->get_element_column() . "` = 0 AND `post_type` = 'page';";
			$this->_model->set_excluded_ids( $this->_db->get_col( $query ) );
			$posts = $this->_model->get_excluded_ids();
		} else {
			$posts = $this->_model->get_posts_excluded();
		}
		$query_request->set( 'post__not_in', array_merge($post__not_in, $posts) );

		return $query_request;
	}

	public function exclude_categories( $args, $taxonomies = array( 'category', ) ) {
		if ( $taxonomies != array( 'category', ) ) {
			return $args;
		}

		return $this->_exclude_elements( $args, $this->_model->get_tax_items( array( 'category',) ) );
	}

	public function exclude_tags( $args, $taxonomies = array( 'post_tag', ) ) {
		if ( $taxonomies != array( 'post_tag', ) ) {
			return $args;
		}

		return $this->_exclude_elements( $args, $this->_model->get_tax_items( array( 'tags', ) ) );
	}

	public function exclude_desktop_links_categories($args) {
		return $this->_exclude_elements( $args, array( 'link_category', ), true );
	}

	public function exclude_mobile_links_categories($args, $taxonomies ) {
		if ( $taxonomies != array( 'link_category', ) ) {
			return $args;
		}

		return $this->_exclude_elements( $args, array( 'link_category', ) );
	}

	public function exclude_links( $args, $taxonomies = array( 'link', ) ) {
		if ( $taxonomies != array( 'link', ) ) {
			return $args;
		}

		return $this->_exclude_elements( $args, array( 'link', ) );
	}

	public function fix_amount_error($end_result_term) {
		$condition =
		$this->_model->is_mobile_device() &&
		is_object($end_result_term) &&
		isset( $end_result_term->term_id )&&
		isset( $end_result_term->taxonomy )&&
		in_array( $end_result_term->taxonomy, $this->_model->get_tax_items( array( 'category', 'tags', ) ) );

		if ( $condition	) {
			$count = intval( $end_result_term->count ) - $this->_model->get_posts_count( $end_result_term->term_id );
			$end_result_term->count = sprintf( '%s', ( $count > 0 ) ? $count : 0 );
		}

		return $end_result_term;
	}

	public function fix_amount_errors($end_result_terms) {
		if ( is_array( $end_result_terms ) ) {
			foreach ( $end_result_terms as $object ) {
				$this->fix_amount_error( $object );
			}
		}

		return $end_result_terms;
	}

	public function exclude_albums($albums) {
		$filtered_albums = array();
		$all_excluded = $this->_model->get_all_excluded();
		foreach ( $albums as $key => $value ) {
			if ( in_array( sprintf( '%s', $key ), $all_excluded ) ) {
				continue;
			}
			$filtered_albums[$key] = $value;
		}

		return $filtered_albums;
	}

	public function exclude_media($media) {
		$this->_model->set_excluded_ids( $this->_model->get_all_excluded() );

		return array_filter( $media, array( $this->_model, 'exclude_media' ) );
	}

	private function _exclude_elements($array_arguments, $taxonomy, $is_different_key = false) {
		$taxonomy_exist = array_merge( array( 'link_category', 'link', ), $this->_model->get_tax_items( array( 'category', 'tags', ) ) );

		if ( ! is_array( $array_arguments ) || ! is_array( $taxonomy ) ) {
			return $array_arguments;
		}
		foreach ( $taxonomy as $item ) {
			if ( ! in_array( $item, $taxonomy_exist ) ) {
				return $array_arguments;
			}
		}

		$query = $this->_model->set_query( $taxonomy );
		$key = 'exclude';
		if ( $taxonomy == array( 'link_category', ) && $is_different_key ) {
			$key = 'exclude_category';
		}
		$array_arguments[$key] = implode( ',', $this->_db->get_col( $query ) );

		return $array_arguments;
	}
}