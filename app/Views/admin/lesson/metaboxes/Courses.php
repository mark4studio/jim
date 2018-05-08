<p>Select the parent Course(s).</p>
<ul class="checkbox-list">
<?php
$qp_course = explode(',', get_post_meta($post->ID, 'qp_course', true));

$loop = new \WP_Query(array('post_type'=>'qp_course','orderby'=>'menu_order', 'order'=>'ASC'));
while($loop->have_posts()){
	$loop->the_post();
	$checked = in_array($loop->post->ID, $qp_course) ? 'checked' : '';
	echo '<li><label><input type="checkbox" name="qp_course[]" value="' . $loop->post->ID .'" '.$checked.'/> '. get_the_title() .'</label></li>';
}
?>
</ul>