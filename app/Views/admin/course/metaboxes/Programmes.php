<p>Select the parent Programme(s).</p>
<ul class="checkbox-list">
<?php
$qp_programme = explode(',', get_post_meta($post->ID, 'qp_programme', true));

$loop = new \WP_Query(array('post_type'=>'qp_programme','orderby'=>'menu_order', 'order'=>'ASC'));
while($loop->have_posts()){
	$loop->the_post();
	$checked = in_array($loop->post->ID, $qp_programme) ? 'checked' : '';
	echo '<li><label><input type="checkbox" name="qp_programme[]" value="' . $loop->post->ID .'" '.$checked.'/> '. get_the_title() .'</label></li>';
}
?>
</ul>