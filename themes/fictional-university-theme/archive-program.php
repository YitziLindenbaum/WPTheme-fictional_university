<?php
get_header();
pageBanner(array(
  'title' => 'All Programs',
  'subtitle' => "We offer a ride range of majors -- what interests you?"
));
?>


<div class="container container--narrow page-section">

    <ul class="link-list min-list">
        <?php
        while (have_posts()) {
            the_post(); ?>
            <li><a href=<?php the_permalink(); ?>><?php the_title(); ?></a></li>
        <?php }
        echo paginate_links();
        ?>
    </ul>
    
</div>

<?php

get_footer();
?>