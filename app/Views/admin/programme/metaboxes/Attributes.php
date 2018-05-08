<p><strong>Order</strong></p>
<label><input type="radio" name="radioPosition"/> Before</label>
<label><input type="radio" name="radioPosition"/> After</label><br>
<select name="selectProgramme" style="margin-top:10px;width:100%">
<?php
$loop = new \WP_Query(array('post_type'=>'qp_programme'));
while($loop->have_posts()){
	$loop->the_post();
	echo '<option value="' . $loop->post->ID .'">'. get_the_title() .'</option>';
}
?>
</select>