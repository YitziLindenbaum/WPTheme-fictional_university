<?php
get_header();
pageBanner(array(
  'title' => 'Search results',
  'subtitle' => 'You searched for &ldquo;' . esc_html(get_search_query(false)) . '&rdquo;'
));
?>


<div class="container container--narrow page-section">
  <?php
  if (have_posts()) {
    while (have_posts()) {
      the_post();
      get_template_part('template-parts/content', get_post_type());
    }
  } else {
    echo '<h2 class="headline headline--small-plus"> No results for search term &ldquo;' . esc_html(get_search_query(false)) . '&rdquo;';
  }

  get_search_form();
  echo paginate_links();
  ?>
</div>

<?php

get_footer();
?>