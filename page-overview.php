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
                <?php if(isset($_POST["delete"])){db_state("disposed");}?>
                <?php if(isset($_POST["consume"])){db_state("consumed");}?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="page-form">
                    <form method="get">
                        <div class="form-group row justify-content-center text-center">
                            <div class="col-md-8 col-lg-4 col-xl-3">
                                <label for="search-box">Zoek met een zoekterm</label>
                                <input type="text" class="form-control" id="search-box" name="search-keyword">
                            </div>
                            <div class="col-md-4 col-lg-4 col-xl-3">
                                <label>Sorteer op categorie</label>
                                <select name="product-category" class="form-control">
                                    <option selected="selected">Selecteer een categorie</option>
                                    <?php db_categories('list','main'); ?>
                                </select>
                            </div>
                            <div class="col-md-8 col-lg-4 col-xl-4">
                                <label>Sorteer op product status</label>
                                <select name="product-state" class="form-control">
                                    <option selected="selected">Selecteer een status</option>
                                    <option value="inventory">Inventaris</option>
                                    <option value="consumed">Geconsumeerd</option>
                                    <option value="disposed">Weggegooid</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-2 col-xl-1">
                                <label>Filter</label>
                                <button type="submit"  title='Filter' class="btn btn-green form-control"><i class="fas fa-filter search-custom"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php db_products_inventory(500,'Expiry_date'); ?>

    </div>
<?php endwhile;

get_footer()

?>
