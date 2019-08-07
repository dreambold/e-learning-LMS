<?php
global $wpdb;
$back_url = bp_core_get_user_domain(get_current_user_id()).'listing/quiz_listing/';

$questionMapper = new WpProQuiz_Model_QuestionMapper();
$pro_quiz_id=get_post_meta($quiz_id, 'quiz_pro_id');
$questions = array();
$enabled_quiz_builder = version_compare(LEARNDASH_VERSION, "2.6.0", ">=") && LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Quizzes_Builder', 'enabled') == 'yes' ? true : false;
if ($enabled_quiz_builder) {
    $questions = learndash_get_quiz_questions($quiz_id);
    // var_dump($questions);
    // die();
} else {
    $questions = @$questionMapper->fetchAll($pro_quiz_id);
}
// $sql = "SELECT meta_key,meta_value FROM $table WHERE user_id = ".get_current_user_id()." AND meta_key like 'wdm_question_id_%'";
// // if (constant('WP_ALLOW_MULTISITE')) {
// if (is_multisite()) {
//     $temp =  $wpdb->prefix;
//     $temp = explode(get_current_blog_id(), $temp);
//     $temp_prefix = $temp[0];
//     $sql = "SELECT meta_key,meta_value FROM ".$temp_prefix."usermeta WHERE user_id = ".get_current_user_id()." AND meta_key like 'wdm_question_id_%'";
// }

// $results = $wpdb->get_results($sql);
//echo "<pre>";print_R($results);echo "</pre>";
$course_result = get_option('wdm_question_create_page');
$link = get_permalink($course_result);
//echo "<pre>";print_r($results);echo "</pre>";
$quiz_list = array();


?>
    <?php if (isset($_GET['quiz_id'])) {
        ?>
        <button style="float:right;margin-bottom: 10px;" onclick="location.href='<?php echo $link.'?quiz_id='.$quiz_id; ?>';"><?php _e('Add new', 'fcc'); ?></button>
       <button style="float:right;margin-bottom: 10px;" onclick="location.href='<?php echo $back_url; ?>';"><?php _e('Back', 'fcc'); ?></button>
        <?php
    } else {
    ?>
    <a class="wdm_link" href="<?php echo $link.'?quiz_id='.$quiz_id; ?>" style="float:right;margin-bottom: 10px;"><?php _e('Add new', 'fcc'); ?></a>
    <?php
}
    ?>
<table id="wdm_question_list">
    <thead>
        <tr>
            <th><?php echo __('Name', 'fcc'); ?></th>
            <th><?php echo __('Type', 'fcc'); ?></th>
            <th><?php echo __('Category', 'fcc'); ?></th>
            <th><?php echo __('Points', 'fcc'); ?></th>
            <th><?php echo __('Edit', 'fcc'); ?></th>
        </tr>
    </thead>
<tfoot>
            <tr>
                <th><?php echo __('Name', 'fcc'); ?></th>
            <th><?php echo __('Type', 'fcc'); ?></th>
            <th><?php echo __('Category', 'fcc'); ?></th>
            <th><?php echo __('Points', 'fcc'); ?></th>
            <th><?php echo __('Edit', 'fcc'); ?></th>
            </tr>
        </tfoot>
    <tbody>
        <?php
        if (!$enabled_quiz_builder) {
            $quiz_idlink = isset($_GET['quiz_id']) ? $_GET['quiz_id'] : $_GET['quizid'];
            foreach ($questions as $value) {
                if (!in_array($value->getAnswerType(), $quiz_list)) {
                    $quiz_list[] = $value->getAnswerType();
                }
                ?>
                <tr>
                    <td><?php echo $value->getTitle(); ?></td>
                    <td><?php echo $value->getAnswerType(); ?></td>
                    <td><?php echo $value->getCategoryName(); ?></td>
                    <td><?php echo $value->getPoints(); ?></td>
                    <td>
                        <a class="wdm_tooltip wdm_edit" href = "<?php echo add_query_arg(array("questionid" => $value->getId(), "quiz_id" => $quiz_idlink), $link); ?>"><img src="<?php echo plugins_url('images/edit.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo __('Edit Question', 'fcc'); ?></span></a>
                        <div class="wdm_tooltip wdm_delete" data-post_id="<?php echo $value->getId(); ?>"><img src="<?php echo plugins_url('images/trash.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo __('Delete Question?', 'fcc'); ?></span></div>
                    </td>
                </tr>
                <?php
            }
        }else{
            $cnt = 0;
            foreach ($questions as $question_id => $pro_question_id) {
                $answer_type = get_post_meta($question_id, 'question_type', true);
                if (!in_array($answer_type, $quiz_list)) {
                    $quiz_list[] = $answer_type;
                }
                $question_pro_category = get_post_meta($question_id, 'question_pro_category', true);
                $question_points = get_post_meta($question_id, 'question_points', true);
                if($question_pro_category && $question_pro_category != 0){
                    $category_mapper = new WpProQuiz_Model_CategoryMapper();
                    $cat = $category_mapper->fetchById( $question_pro_category );
                    if ( ( $cat ) && ( is_a( $cat, 'WpProQuiz_Model_Category' ) ) ) {
                        $question_pro_category = $cat->getCategoryName();
                    }else{
                        $question_pro_category = '';
                    }
                }
                ?>
                <tr>
                    <td><?php echo get_the_title($question_id); ?></td>
                    <td><?php echo $answer_type; ?></td>
                    <td><?php echo $question_pro_category; ?></td>
                    <td><?php echo $question_points; ?></td>
                    <td>
                        <a class="wdm_tooltip wdm_edit" href = "<?php echo add_query_arg(array("questionid" => $pro_question_id, "post_id" => $question_id, 'quiz_id' => $_GET['quiz_id']), $link); ?>"><img src="<?php echo plugins_url('images/edit.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo __('Edit Question', 'fcc'); ?></span></a>
                        <div class="wdm_tooltip wdm_delete" data-post_id="<?php echo $question_id; ?>"><img src="<?php echo plugins_url('images/trash.png', dirname(dirname(__FILE__))); ?>" width="25" height="25"><span class="wdm_tooltiptext"><?php echo __('Delete Question?', 'fcc'); ?></span></div>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
    </tbody>
</table>
<?php
// $sql = "SELECT post_title FROM {$wpdb->prefix}posts WHERE post_author = ".get_current_user_id()." AND post_type like 'sfwd-quiz' AND post_status IN ('draft','publish')"; 
// $results = $wpdb->get_col($sql);
// //echo '<pre>';print_R($results);echo '</pre>';
// if(!empty($results)){
// foreach ($results as $k=>$v){
//  if(!in_array($v,$quiz_list)){
//  $quiz_list[] = $v;
//  }
// }
// }

$quiz_list[] = '-';
$temp = implode("','", $quiz_list);
$quiz_names = "['".$temp."']";
?>
<script>
var question_list_datatable;
jQuery(document).ready(function($){

question_list_datatable = $('#wdm_question_list').dataTable().columnFilter({
            aoColumns: [ { type: "text" },
                     { type: "select",
                     values: <?php echo $quiz_names; ?>
                },
                     { type: "select" },
                     null,
                     null
                ]

        });
});
</script>