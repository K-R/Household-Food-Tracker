    <div class="col-lg-6">
        <div class="page-title">
            Nieuw product
        </div>
        <div class="page-text">
            Voeg een nieuw product toe aan de database.
        </div>
        <?php if((isset($_POST['add'])) and (!empty($_POST['expiry-date'])) and (is_numeric($_POST['quantity']))){db_new_add();}?>
        <div class="page-form">
            <form method="post">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Naam</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="product-name">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Prijs</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="product-price">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Gewicht</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="product-amount">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Eenheid</label>
                    <div class="col-sm-10">
                        <select name="product-unit" class="form-control">
                            <option>kilo</option>
                            <option>gram</option>
                            <option>liters</option>
                            <option>mililiters</option>
                            <option>centiliters</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Houdbaar- heidsdatum</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" name="expiry-date">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Hoeveelheid</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="quantity">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Categorie</label>
                    <div class="col-sm-10">
                        <select name="product-sub-category" class="form-control">
                            <?php db_categories('list','sub'); ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-2">
                        <button type="submit" title='Toevoegen' class="btn btn-green" name="add">Opslaan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>