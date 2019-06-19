<div class="page-form">
    <form method="get" action="<?php echo get_permalink( get_page_by_title( 'Product vanuit database' ));?>">
        <div class="form-group row justify-content-center">
            <div class="col-sm-6">
                <input type="text" class="form-control" id="search-input" name="search-input">
            </div>
            <div class="col-sm-2">
                <button type="submit"  title='Zoek' name="search" class="btn btn-green form-control"><i class="fas fa-search search-custom"></i></button>
            </div>
        </div>
    </form>
</div>