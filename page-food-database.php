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

    <div class="row">
        <div class="col-sm-12">
            <?php if((isset($_POST['add'])) and (!empty($_POST['expiry-date'])) and (is_numeric($_POST['quantity']))){db_existing_add();}?>
        </div>
    </div>

    <?php get_search_form();?>

    <?php if(isset($_GET['search'])){db_search($_GET['search-input']);} ?>

</div>

    <?php endwhile;
get_footer()

?>