<?php

get_header();

    while (have_posts()) : the_post(); ?>
    <div class="container">

        <div class="row">
            <div class="col-sm-12">
                <div class="page-title">
                    <?php echo the_title()?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="page-text">
                    <?php echo the_content()?>
                </div>
            </div>
        </div>

    </div>
<?php endwhile;

get_footer()

?>

