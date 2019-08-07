<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(!class_exists('attachaway_column'))
{
	class attachaway_column
	{
		public function __construct()
		{
			if(is_admin()
			and function_exists('add_action')
			and function_exists('add_filter')
			and function_exists('get_taxonomies')
			and function_exists('get_post_types')
			and function_exists('version_compare'))
			{
				add_action('admin_init', array($this, 'column'));
				add_action('admin_head', array($this, 'css')); 
			}
		}
		public function column()
		{
			global $wp_version;
			$is_wp_v3_1 = version_compare($wp_version, '3.0.999', '>');
			add_filter('manage_media_columns', array($this, 'add'));
			add_action('manage_media_custom_column', array($this, 'value'), 10, 2);
			add_filter('manage_link-manager_columns', array($this, 'add'));
			add_action('manage_link_custom_column', array($this, 'value'), 10, 2);
			add_action('manage_edit-link-categories_columns', array($this, 'add'));
			add_filter('manage_link_categories_custom_column', array($this, 'returnvalue'), 10, 3);
			foreach(get_taxonomies() as $taxonomy)
			{
				add_action("manage_edit-${taxonomy}_columns", array($this, 'add'));
				add_filter("manage_${taxonomy}_custom_column", array($this, 'returnvalue'), 10, 3);
				if($is_wp_v3_1) add_filter("manage_edit-${taxonomy}_sortable_columns", array($this, 'add'));
			}
			foreach(get_post_types() as $post_type)
			{
				add_action("manage_edit-${post_type}_columns", array($this, 'add'));
				add_filter("manage_${post_type}_posts_custom_column", array($this, 'value'), 10, 3);
				if($is_wp_v3_1) add_filter("manage_edit-${post_type}_sortable_columns", array($this, 'add')); 
			}
			add_action('manage_users_columns', array($this, 'add'));
			add_filter('manage_users_custom_column', array($this, 'returnvalue'), 10, 3);
			if($is_wp_v3_1) add_filter("manage_users_sortable_columns", array($this, 'add')); 
			add_action('manage_edit-comments_columns', array($this, 'add'));
			add_action('manage_comments_custom_column', array($this, 'value'), 10, 2);
			if($is_wp_v3_1) add_filter("manage_edit-comments_sortable_columns", array($this, 'add')); 
		}
		public function add($columns)
		{
			return array_slice($columns, 0, 1, true) + array('attachaway_id' => 'ID') + array_slice($columns, 1, NULL, true);
		}
		public function value($column_name, $id)
		{ 
			if($column_name === 'attachaway_id') echo $id;  
		}
		public function returnvalue($value, $column_name, $id)
		{ 
			if($column_name === 'attachaway_id') $value .= $id; 
			return $value; 
		}
		public function css()
		{ 
			echo PHP_EOL.'<style type="text/css"> table.widefat th.column-attachaway_id{width: 50px;} </style>'.PHP_EOL; 
		}
	}
}