<style>
    th {
        padding: 25px 10px;
    }
    td {
        padding: 0 10px;
    }
    table {
        box-shadow: 0px 0px 15px 10px rgba(0, 0, 0, 0.28);
    }
    .table-header {
        background:linear-gradient(-30deg, #ecd417 , #ffe82b);
    }
    tr:nth-child(odd) {
        background-color: #f3f3f3;
    }
    tr:nth-child(even) {
        background-color: #fff;
    }
</style>
<table>
    <caption>
    </caption>
    <tr class="table-header">
        <th style="width:20%;">Seminar/Webinar/Course Title</th>
        <th style="width:20%;">Date</th>
        <th style="width:20%;">Free places</th>
        <th style="width:20%;">Meeting place</th>
        <th style="width:20%;"></th>
    </tr>
    <?php
    $current_user = get_current_user_id();
    $current_user = serialize("{$current_user}");
    $offline      = get_posts([
        'post_type' => 'offline-courses',
        'posts_per_page' => 4,
        'suppress_filters' => true,
        'order' => 'DESC',
        'orderby' => 'post_date',
        'post_status' => 'publish',
        'no_found_rows' => true,
        'meta_query' => [
            [
                'key' => '_invited_users',
                'value' => $current_user,
                'compare' => 'LIKE',
            ]
        ]
    ]);
    $webinars     = get_posts([
        'post_type' => 'webinars',
        'posts_per_page' => 4,
        'suppress_filters' => true,
        'order' => 'DESC',
        'orderby' => 'post_date',
        'post_status' => 'publish',
        'no_found_rows' => true,
        'meta_query' => [
            [
                'key' => '_invited_users',
                'value' => $current_user,
                'compare' => 'LIKE',
            ]
        ]
    ]);
    ?>
    <?php foreach (array_merge($offline, $webinars) as $post): ?>
        <?php
        $invited_users   = get_post_meta($post->ID, '_invited_users', true);
        $occupied_places = (is_array($invited_users) ? count($invited_users) : 0);
        if($post->post_type == 'webinars'){
             $type = '_webinar';
             $value_place     = get_post_meta($post->ID, '_free_places', true) - $occupied_places;
             
        }elseif($post->post_type == 'offline-courses'){
            $type = '_offline';
            $value_place     = get_post_meta($post->ID, '_course_free_places', true) - $occupied_places;
        }
        $value_date      = get_post_meta($post->ID, "{$type}_date", true);
        $value_date_end  = get_post_meta($post->ID, "{$type}_date_end", true);
        $value_location  = get_post_meta($post->ID, '_location_value', true);
        $value_adress    = get_post_meta($post->ID, '_location_adress', true);
        
        ?>

        <tr id="post-<?php echo $post->ID; ?>">
            <td><?php echo $post->post_title; ?></td>
            <td>
                <?php
                if ($value_date != '') {
                    echo 'From: '.$value_date.'<br>To: '.$value_date_end;
                }
                ?>
            </td>
            <td>
                <?php
                if ($value_place != '') {
                    echo $value_place;
                }
                ?>
            </td>
            <td>
                <?php
                if ($value_location != '') {
                    echo '<b>'.$value_location.'</b><br>'.$value_adress;
                }
                ?>
            </td>
            <td>
                <a class="btn btn-default" href="<?php echo get_permalink($post) ?>">More</a>
                <?php
                if (current_user_can('delete_posts')) {
                    echo '<a  class="btn btn-default" href="'.get_delete_post_link($post->ID).'">Delete</a>';
                }
                ?>
            </td>
        </tr><!-- post -->
    <?php endforeach; ?>

</table>