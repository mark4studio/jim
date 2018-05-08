<p>Select the courses that has to be completed before accessing the current course.</p>
<ul class="checkbox-list">
<?php
$qp_prerequisites = explode(',', get_post_meta($post->ID, 'qp_prerequisites', true));

$loop = new \WP_Query(array('post_type'=>'qp_course','orderby'=>'menu_order', 'order'=>'ASC'));
while($loop->have_posts()){
	$loop->the_post();
	$checked = in_array($loop->post->ID, $qp_prerequisites) ? 'checked' : '';
	echo '<li><label><input type="checkbox" name="qp_prerequisites[]" value="' . $loop->post->ID .'" '.$checked.'/> '. get_the_title() .'</label></li>';
}
?>
</ul>