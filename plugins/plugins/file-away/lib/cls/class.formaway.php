<?php
defined('fileaway') or die('Water, water everywhere, but not a drop to drink.');
if(class_exists('fileaway_attributes') && !class_exists('formaway'))
{
	class formaway extends fileaway_attributes
	{
		public function __construct()
		{
			parent::__construct();
			add_shortcode('formaway_open', array($this, 'open'));
			add_shortcode('formaway_row', array($this, 'row'));
			add_shortcode('formaway_cell', array($this, 'cell'));
			add_shortcode('formaway_close', array($this, 'close'));						
		}
		public function open($atts)
		{	
			$get = new fileaway_definitions;
			extract($this->correct(wp_parse_args($atts, $this->formaway_open), $this->shortcodes['formaway_open'], 'formaway_open'));
			if($this->op['javascript'] == 'footer') $GLOBALS['fileaway_add_scripts'] = true;
			if($this->op['stylesheet'] == 'footer') $GLOBALS['fileaway_add_styles'] = true;
			include fileaway_dir.'/lib/inc/inc.formaway-config.php';			
			$thefiles .= $clearfix."<div id='$name' class='ssfa-meta-container $mobileclass $classes' data-uid='$uid' style='margin: 10px 0 20px; $fadeit $howshouldiputit'>";
			include fileaway_dir.'/lib/inc/inc.precontent.php';
			$thefiles .= "<script>jQuery(function(){ jQuery('.footable').footable();});</script>".
				"<table id='ssfa-table-$uid' data-filter='#filter-$uid' class='footable ssfa-sortable $theme $textalign' $page $disablesort>".
					"<thead><tr>";
			$columns = array();
			$c = 1;
			while($c <= $numcols)
			{
				if(isset($atts['col'.$c]))
				{
					$colname = $atts['col'.$c];
					$colclass = isset($atts['col'.$c.'class']) ? $atts['col'.$c.'class'] : false;
					$coltype = isset($atts['col'.$c.'type']) ? $atts['col'.$c.'type'] : 'alpha';
					$colsort = $initialsort == $c ? $initsort : null;
					$colsort = isset($atts['col'.$c.'sort']) && $atts['col'.$c.'sort'] == 'ignore' ? 'data-sort-ignore="true"' : $colsort;
					$columns[] = '<th class="'.$colclass.'" data-type="'.$coltype.'" '.$colsort.'>'.$colname.'</th>';
				}
				$c++;
			}
			$thefiles .= implode($columns);
			$thefiles .= "</tr></thead><tfoot><tr><td colspan='100'>$pagearea</td></tr></tfoot><tbody>"; 
			return $thefiles;
		}
		public function row($atts, $content = null)
		{
			$classes = isset($atts['classes']) ? ' class="'.$atts['classes'].'"' : null;
			return '<tr'.$classes.'>'.do_shortcode($content).'</tr>';	
		}
		public function cell($atts, $content = null)
		{
			$classes = isset($atts['classes']) ? ' class="'.$atts['classes'].'"' : null;
			$colspan = isset($atts['colspan']) && is_numeric($atts['colspan']) ? ' colspan="'.round($atts['colspan'], 0).'"' : null;
			$datavalue = isset($atts['sortvalue']) ? ' data-value="'.$atts['sortvalue'].'"' : null;
			return '<td'.$classes.$colspan.$datavalue.'>'.do_shortcode($content).'</td>';	
		}
		public function close($atts)
		{
			$clearfix = isset($atts['clearfix']) ? '<div class="ssfa-clearfix"></div>' : null;
			return "</tbody></table></div></div>$clearfix";
		}
	}
}