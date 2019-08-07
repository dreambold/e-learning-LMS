<?php
/**
 * Class Woffice_Search
 *
 *
 * @since 2.7.8
 */
if( ! class_exists( 'Woffice_Search' ) ) {
	class Woffice_Search
	{

		/**
		 * The queried search term
		 *
		 * @var string
		 */
		private $search = '';

		/**
		 * The result data
		 *
		 * @var array
		 */
		private $results = array();

		/**
		 * The queries search term
		 *
		 * @var array
		 */
		private $types = array();


		/**
		 * Woffice_Search constructor
		 */
		public function __construct()
		{
			add_action( 'wp_ajax_woffice_search', array($this, 'searchExcludingTypes'));
			add_action( 'wp_ajax_nopriv_woffice_search', array($this, 'searchExcludingTypes'));

            add_filter( 'woffice_js_exchanged_data', array($this, 'exchanger'));

            add_action( 'wp_enqueue_scripts', function () {
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-ui-autocomplete' );
			});
		}

        /**
         * Pass data to the client
         *
         * @param array $data
         * @return array
         */
        public function exchanger($data) {
            /**
             * Filter: `woffice_has_live_search`
             *
             * @return boolean
             */
            $data['has_live_search'] = apply_filters('woffice_has_live_search', true);

            return $data;
        }

		/**
		 * Run the search in the database and return the results in a JSON encoded response
		 *
		 * @return void
		 */
		public function searchExcludingTypes()
		{
			$this->search = strtolower(esc_attr($_POST['search']));

            // Get all registered post types
            $all_post_types = array_values(get_post_types());

            // Add prefix if prefix not exists
            foreach ($all_post_types as $key_name => $val){
                if (strpos($val, 'wp_') !== 0)
                    $all_post_types[$key_name] = 'wp_' . $val;
            }

            // Add buddy press post types
            array_push($all_post_types, 'bp_members', 'bp_groups');

            $includes_types = $all_post_types;

            // Remove selected post types
            if (isset($_POST['types']) && $_POST['types'] !== 'all') {
                $exclude_types = explode(',', $_POST['types']);
                $includes_types = array_diff($all_post_types, $exclude_types);
            }

            /**
			 * Filter `woffice_search_post_types`
			 * This is the available post types in which Woffice will make its query
			 * Please apply "wp_" before any new post type
			 *
			 * @param array
			 */
			$avilable_types = apply_filters('woffice_search_post_types', $includes_types);

			if ($_POST['types'] === 'all') {
				$this->types = $avilable_types;
			} else {
				$types = explode(',', $_POST['types']);
				foreach ($types as $type) {
					if (!isset( $avilable_types[$type])) {
						continue;
					}

					$this->types[] = $type;
				}
			}

			$this->fetch();

			if (empty($this->results)) {
				$this->results['empty'] = array(
					'icon'  => 'fa-close',
					'label' => __('There was no result matching your query, please try again with another search.', 'woffice'),
				);
			}

			/**
			 * Filter `woffice_search_results`
			 *
			 * @param array - the returned results
			 * @param string - the search query term
			 * @param array - the queried types
			 */
			$results = apply_filters('woffice_search_results', $this->results, $this->search, $this->types);

			wp_die(json_encode($results));
		}

		/**
		 * Fetch the results for the current instance
		 */
		private function fetch()
		{
			foreach ($this->types as $type) {

				// Either `bp_` or `wp_`
				$source = substr($type, 0, 3);

				$type   = substr($type, 3);
				$items  = array();

				// BuddyPress case
				if ($source === 'bp_' && function_exists('bp_is_active')) {
					switch ($type) {
						case 'members':
							$members = new BP_User_Query(array(
								'search_terms' => $this->search
							));

							foreach ($members->results as $member) {
								$items[] = $this->format(array(
									'id'    => $member->id,
									'title' => woffice_get_name_to_display($member->id),
									'link'  => bp_core_get_user_domain($member->id),
								), $type);
							}
							break;
						case 'groups':
							if (bp_is_active('groups')) {
								$groups_query = BP_Groups_Group::get( array(
									'search_terms' => $this->search
								));

								foreach ($groups_query['groups'] as $group) {
									$items[] = $this->format(array(
										'id'    => $group->id,
										'title' => $group->name,
										'link'  => bp_get_group_permalink($group),
									), $this);
								}
							}
							break;
					}
				}

				// WordPress case
				else {
					$loop = new WP_Query(array(
						's'         => $this->search,
						'post_type' => array($type)
					));

					while($loop->have_posts()) {

						$loop->the_post();

						/**
						 * Filter `woffice_search_item_visibility`
						 *
						 * @param boolean - whether it is displayed or not
						 * @param int - the item's id
						 * @param string - the item's type
						 */
						$is_visible = apply_filters('woffice_search_item_visibility', true, get_the_id(), $type);

						if (!$is_visible) {
							continue;
						}

						$meta = get_the_date() .' &mdash; '. __('by', 'woffice') .' '. get_the_author();
						$excerpt = get_the_excerpt();
						if ($excerpt) {
							$meta .= ' &mdash; '. get_the_excerpt();
						}

						$items[] = $this->format(array(
							'id'    => get_the_id(),
							'title' => get_the_title(),
							'link'  => get_the_permalink(),
							'meta'  => $meta
						), $type);

					}

					wp_reset_postdata();
				}

				if (empty($items)) {
					continue;
				}

				$this->results[$type] = array(
					'items' => $items,
					'icon'  => $this->getIcon($type),
					'label' => $this->getLabel($type),
				);

			}
		}

		/**
		 * Format a single item to be displayed in the frontend
		 *
		 * @param array $item
		 * @param string $type
		 *
		 * @return array
		 */
		private function format($item, $type)
		{
			if ($type === 'product') {
				$product = wc_get_product($item['id']);
				$item['meta'] = wc_price($product->get_price() ) .' &mdash; '. $item['meta'];
			}

			if ($type === 'members') {
				$item['meta'] = __('Joined', 'woffice') . ' '. date('M Y', strtotime(get_userdata($item['id'])->user_registered));
			}

			$item['meta'] = wp_strip_all_tags($item['meta']);

			return $item;
		}

		/**
		 * Get the Font Awesome icon for a given type
		 *
		 * @param $type
		 *
		 * @return string
		 */
		private function getIcon($type)
		{
			/**
			 * Filter `woffice_search_custom_icon`
			 *
			 * Controls the icon displayed for a given type
			 *
			 * @param null|string $value
			 * @param string $type
			 */
			$custom_icon = apply_filters('woffice_search_custom_icon', null, $type);

			if ($custom_icon) {
				return $custom_icon;
			}


			switch($type) {
				case 'page':
					return 'fa-file-text';
					break;
				case 'post':
					return 'fa-newspaper-o';
					break;
				case 'directory':
					return 'fa-folder';
					break;
				case 'project':
					return 'fa-briefcase';
					break;
				case 'wiki':
					return 'fa-book';
					break;
				case 'ajde_events':
					return 'fa-calendar';
					break;
				case 'forum':
					return 'fa-sitemap';
					break;
				case 'topic':
					return 'fa-comment';
					break;
				case 'product':
					return 'fa-shopping-bag';
					break;
				case 'pec-events':
					return 'fa-calendar';
					break;
				case 'activity':
					return 'fa-comment-alt';
					break;
				case 'members':
					return 'fa-vcard';
					break;
				case 'groups':
					return 'fa-users';
					break;
				default:
					return 'fa-file';
			}
		}

		/**
		 * Get the Label for a given type
		 *
		 * @param $type
		 *
		 * @return string
		 */
		private function getLabel($type)
		{
			switch($type) {
				case 'page':
					return __('Pages', 'woffice');
					break;
				case 'post':
					return __('Posts', 'woffice');
					break;
				case 'directory':
					return __('Items', 'woffice');
					break;
				case 'project':
					return __('Projects', 'woffice');
					break;
				case 'wiki':
					return __('Wiki articles', 'woffice');
					break;
				case 'ajde_events':
					return __('Events', 'woffice');
					break;
				case 'forum':
					return __('Forums', 'woffice');
					break;
				case 'topic':
					return __('Topics', 'woffice');
					break;
				case 'product':
					return __('Products', 'woffice');
					break;
				case 'pec-events':
					return __('Events', 'woffice');
					break;
				case 'activity':
					return __('Activity items', 'woffice');
					break;
				case 'members':
					return __('Members', 'woffice');
					break;
				case 'groups':
					return __('Groups', 'woffice');
					break;
				default:
					return __('Items', 'woffice');
			}
		}
	}
}
/**
 * Let's fire it :
 */
new Woffice_Search();