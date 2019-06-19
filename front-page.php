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
            <div class="col-lg-4">
                <div class="page-text">
                    % weggegooid voedsel van totaal: <br>
                    <b style="color:red;"><?php $disposed_percentage =  round((db_weight_total("disposed")/db_weight_total(NULL))*100,2); echo $disposed_percentage; ?></b>
                    %<!--<i class="fa fa-percent"></i>-->
                </div>
            </div>
            <div class="col-lg-4">
                <div class="page-text">
                    Totale uitgaven voedsel : <br>
                    <i class="fa fa-euro-sign"></i>
                    <?php $euros =  db_pie_chart_euro(); echo $euros[3]; ?>
                    ,-
                </div>
            </div>
            <div class="col-lg-4">
                <div class="page-text">
                    Totale uitstoot voedselaankopen: <br>
                    <i class="fa fa-industry"></i>
                    <?php $CO2 =  db_CO2_total(); echo $CO2; ?>
                    of kilogram(s) CO<sub>2</sub>e
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <?php db_expired_products()?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <?php include('partials/pie-chart.php');?>
            </div>
            <div class="col-lg-6">
                <?php include('partials/bar-chart.php');?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
               <div class="page-title">
                   Recent toegevoegde producten
               </div>
            </div>
        </div>
        <?php if(isset($_POST["delete"])){db_state("disposed");}?>
        <?php if(isset($_POST["consume"])){db_state("consumed");}?>
        <?php echo db_products_inventory(3,'Entry_date');?>

    </div>
<?php endwhile;

get_footer()

?>