<?php

function db_existing_add(){

    include('partials/config.php');
    $conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $product = $_POST['add'];
    $expiry_date = $_POST['expiry-date'];
    $quantity = $_POST['quantity'];

    $query = "INSERT INTO `entries` (`Entry_ID`, `Entry_date`, `Expiry_date`, `State`,`Quantity`, `Product_ID`)
              VALUES (NULL,NULL,'$expiry_date','inventory','$quantity','$product')";

    if ($conn->query($query) === TRUE) {
        echo "<div class='page-alert'>Bestaand product is toegevoegd, check de <a href='".get_permalink( get_page_by_title( 'voedselbeheer' ) )."' style='text-decoration: underline'>Voedselbeheer</a> pagina</div>";
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }

    $conn->close();
}

function db_new_add(){

    include('partials/config.php');
    $conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $categories = db_sub_categories('array','sub');

    $category = intval(array_search($_POST['product-sub-category'], $categories)) + 1;

    $query = "INSERT INTO `products` (`Product_ID`, `Name`, `Price`, `Amount`,`Unit`, `Sub_category_ID`)
              VALUES (NULL,'".$_POST['product-name']."','".$_POST['product-price']."','".$_POST['product-amount']."','".$_POST['product-unit']."',".$category.");";
    $query .= "INSERT INTO `entries` (`Entry_ID`, `Entry_date`, `Expiry_date`, `State`,`Quantity`, `Product_ID`) 
               SELECT NULL,NULL,'".$_POST['expiry-date']."','inventory','".$_POST['quantity']."', Product_ID
               FROM products WHERE  Name= '".$_POST['product-name']."';";

    if (mysqli_multi_query($conn,$query)) {
        echo "<div class='page-text'>Nieuw product is toegevoegd, check de voedselbeheer pagina</div>";
    }else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }

    $conn->close();
}

function db_categories($output,$category_type){
    include('partials/config.php');
    $conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if($category_type == 'sub'){
        $query = "SELECT name from sub_categories;";
    }
    elseif($category_type == 'main'){
        $query = "SELECT name from categories;";
    }

    $result = $conn->query($query);

    $category_arr = array();

    if ($result->num_rows > 0) {

        while($row = $result->fetch_assoc()) {

            if($output == 'list'){
                echo "<option>".$row['name']."</option>";
            }
            elseif($output == 'array'){
                array_push($category_arr,$row['name']);
            }
        }
    }
    else {
        echo "<div class='page-text'>Failed to load categories</div>";
    }

    if($output == 'array'){return $category_arr;}

    $conn->close();
}

function db_quantity($entry_id){

    include('partials/config.php');
    $conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT Quantity FROM entries WHERE Entry_ID = '".$entry_id."' ";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {

        while($row = $result->fetch_assoc()) {
            $quantity = $row['Quantity'];
        }
    }
    else {
        echo "<div class='page-text'>No data available please add some products</div>";
    }

    return $quantity;
    $conn->close();
}

function db_products_inventory($limit, $type_of_date){

    include('partials/config.php');
    $conn = new mysqli($servername, $username, $password, $dbname);

    mysqli_set_charset($conn ,"utf8");

// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $conditions = array();
    $btn_state = "enabled";

    if((isset($_GET['product-state'])) AND ($_GET['product-state']!='Selecteer een status')){
        $filter_state = $_GET['product-state'];
        $condition_state = "State = '".$filter_state."'";
        array_push($conditions,$condition_state);

        if(($filter_state == 'disposed') or ($filter_state == 'consumed')){
            $btn_state = "disabled";
            if($filter_state == 'disposed'){
                $bg_state = "bg-danger";
                $title_state= "Weggegooide producten";
            }
            else{
                $bg_state = "bg-success";
                $title_state= "Geconsumeerde producten";
            }
        }
        else{
            $bg_state = "bg-primary";
            $title_state= "Producten in inventaris";
        }
    }
    else{
        $condition_state = "State = 'inventory'";
        array_push($conditions,$condition_state);
        $bg_state = "bg-primary";
        $title_state= "Producten in inventaris";
    }

    if((isset($_GET['search-keyword']) AND ($_GET['search-keyword'] != ""))){
        $filter_keyword = $_GET['search-keyword'];
        $condition_keyword = "products.name LIKE '%".$filter_keyword."%'";
        array_push($conditions,$condition_keyword);
    }

    if((isset($_GET['product-category'])) AND ($_GET['product-category']!='Selecteer een categorie')){
        $filter_category = $_GET['product-category'];
        $condition_category = "categories.Name = '".$filter_category."'";
        array_push($conditions, $condition_category);
    }

    if(sizeof($conditions) == 1){
        $WHERE_clause = "WHERE " . $conditions[0];
    }
    elseif(sizeof($conditions)>1){
        $WHERE_clause = "WHERE ";
        for($i = 0; $i < count($conditions); ++$i){
            $WHERE_clause .= $conditions[$i];
            if($i < count($conditions)-1){
                $WHERE_clause .= " AND ";
            }

        }
    }

    $query = "SELECT Entry_ID, products.name, categories.Name, categories.Color, products.Amount, 
                    products.Unit, products.Price, sub_categories.CO2, Entry_date, Expiry_date, Quantity 
                    FROM entries 
                    JOIN products ON entries.Product_ID = products.Product_ID 
                    JOIN sub_categories ON products.Sub_category_ID = sub_categories.Sub_category_ID 
                    JOIN categories ON sub_categories.Category_ID = categories.Category_ID
                    ".$WHERE_clause." ORDER BY ".$type_of_date." LIMIT ".$limit."";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {

        if(is_front_page() == False){
            echo "<div class='row'>
                <div class='col-sm-12'>
                    <div class='page-title ".$bg_state." text-white'>
                        ".$title_state."
                    </div>
                </div>
              </div>";
        }

        while($row = $result->fetch_assoc()) {

            $categories = array('Aardappel, rijst, pasta','Vlees, vis, vegetarisch','Groente','Fruit','Koken, soepen, maaltijden','Diepvries','Brood, cereals, beleg','Zuivel, eieren, boter','Koek, gebak, snoep, chips','Fris, sap, koffie, thee');
            $images = array('carbohydrate.svg','proteins.svg','vegetables.svg','harvest.svg','hotpot.svg','freezer.svg','bread.svg','dairy-products.svg','sweets.svg','drinking.svg');

            foreach($categories as $category){
                if($category == $row['Name']){
                    $key = array_search($category, $categories);
                    $image = "/images/".$images[$key];
                }
            }

            $date = new DateTime(date("y-m-j",strtotime($row['Expiry_date'])));
            $now = new DateTime();

            if($date < $now){
                $bgdate = "red";
                $textdate = "white";
                $message = "Expired: ";
            }
            else{
                $bgdate = "white";
                $textdate = "black";
                $message = "Expires: ";
            }

            echo "<div class='row no-gutters justify-content-center'>
                    <div class='col-lg-4 col-md-4 col-sm-8 col-xs-12'>
                        <div class='inventory-col'>
                            <div class='category-icon' style='background-color: ".$row['Color']."'>
                                <img title='".$row['Name']."' src='".get_template_directory_uri().$image."'>
                            </div>
                            <div class='inventory-name'>".strval($row['Quantity'])." x ".$row['name']."</div>     
                        </div>
                    </div>
                    <div class='col-lg-1 col-md-2 col-sm-4 col-xs-6'>
                        <div class='inventory-col xs-screen'>
                            <form method='get' action='".get_permalink((get_page_by_title('Product')))."'>
                                <div class='form-row inventory-edit-form'>
                                    <div class='form-group col'>
                                        <button type='submit' title='Aanpassen' name='edit' value='" . $row["Entry_ID"] . "' class='btn btn-sm btn-primary form-control'><i class='fas fa-edit'></i></button>
                                    </div>
                                </div>
                            </form> 
                        </div>    
                    </div>
                    <div class='col-lg-1 col-md-2 col-sm-4 col-xs-6'>
                        <div class='inventory-col small-screen'>
                            <div class='inventory-dates' style='background-color:".$bgdate.";color:".$textdate.";'>".$message. date("j-m", strtotime($row['Expiry_date']))."</div>
                        </div>
                    </div>  
                    <div class='col-lg-3 col-md-4 col-sm-8 col-xs-8'>
                        <div class='inventory-col small-screen'>
                            <div class='inventory-weight'><i class='fas fa-balance-scale'></i> ".strval(round($row['Amount']*$row['Quantity'],2))." ".$row['Unit']."</div>
                            <div class='inventory-euro'><i class='fas fa-euro-sign'></i> ".round($row['Price']*$row['Quantity'],2).",-</div>
                            <div class='inventory-CO2'><i class='fas fa-industry'></i> ".round($row['CO2']*($row['Quantity']*weight_converter($row['Amount'],$row['Unit'])),2)." kg CO<sub>2</sub>e</div> 
                        </div>
                    </div>
                    <div class='col-lg-3'>
                        <div class='inventory-col medium-screen '>
                            <form method='post'>
                                    <div class='form-row inventory-state-form'>
                                        <div class='form-group col'><input type='text' class='form-control' value='".$row['Quantity']."' name='state-quantity'></div>
                                        <div class='form-group col'><button type='submit' title='Weggegooid' name='delete' value='" . $row["Entry_ID"] . "' class='btn btn-sm btn-delete form-control' ".$btn_state."><i class='fas fa-trash'></i></button></div>
                                        <div class='form-group col'><button type='submit' title='Geconsumeerd' name='consume' value='" . $row["Entry_ID"] . "' class='btn btn-sm btn-green form-control' ".$btn_state."><i class='fas fa-utensils'></i></button></div>
                                    </div> 
                            </form>
                        </div>
                    </div>
                  </div>";
        }


    } else {
        echo "<div class='page-text'>Geen data beschikbaar voeg producten toe of pas je sorteer opties aan</div>";
    }
    $conn->close();
}

function db_search($search_query){
    include('partials/config.php');
    $conn = new mysqli($servername, $username, $password, $dbname);

    mysqli_set_charset($conn ,"utf8");

// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT products.name, products.Product_ID, sub_categories.Name, sub_categories.Color, products.Amount, 
                    products.Unit, products.Price, sub_categories.CO2
                    FROM products 
                    JOIN sub_categories ON products.Sub_category_ID = sub_categories.Sub_category_ID 
                    WHERE products.name LIKE '%".$search_query."%' LIMIT 500";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {

        $total = mysqli_num_rows($result);

        echo "<div class='page-text'>".$total." Resultaten gevonden met de zoekterm: '".$search_query."'</div>";

        while($row = $result->fetch_assoc()) {

            echo "<div class='row'>";
            echo "<div class='col-md-5'><div class='product-name'>". $row['name'] ."</div></div>";
            echo "<div class='col-md-5'>
                            <form method='post'>
                                    <div class='form-row search-form'>
                                     <div class='form-group col'>
                                        <input type='date' class='form-control' id='search-input' name='expiry-date'>
                                     </div>
                                     <div class='form-group col'>
                                        <input type='number' class='form-control' id='search-input' name='quantity' placeholder='Hoeveelheid'>
                                     </div>
                                     <div class='form-group col'>
                                        <button type='submit' name='add' value=' ".$row['Product_ID']." ' class='btn btn-green form-control'><i class='fas fa-plus'></i></button>
                                     </div>
                                </div>
                                </form>
                                
                      </div>";
            echo "<div class='col-md-2'><div class='product-dates'>€" . $row['Price'] . ",- (".$row['CO2']*(1*weight_converter($row['Amount'],$row['Unit']))." kg CO<sub>2</sub>e)</div></div>";
            echo "</div>";
        }
    }
    else {
        echo "<div class='page-text'>Geen resultaten werden gevonden, verander alstublieft uw zoekterm</div>";
    }
    $conn->close();

}

function db_state($state){

    include('partials/config.php');
    $conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if(isset($_POST['delete'])){
        $entry_id = $_POST['delete'];
    }
    else{
        $entry_id = $_POST['consume'];
    }

    $input_quantity = $_POST['state-quantity'];
    $available_quantity = db_quantity($entry_id);
    $new_quantity = floatval($available_quantity)-$input_quantity;

    if ($input_quantity >= $available_quantity){
        $query = "UPDATE entries SET state = '".$state."' WHERE Entry_id = ".$entry_id."";
        $result = $conn->query($query);

        if ($result) {
            echo "<div class='page-text'>Succesfully $state, refresh page to view results</div>";
        }
        $conn->close();
    }
    else{
        $query = "UPDATE entries SET quantity = '".$new_quantity."' WHERE Entry_id = ".$entry_id.";";
        $query .= "INSERT INTO `entries` (`Entry_ID`, `Entry_date`, `Expiry_date`, `State`,`Quantity`, `Product_ID`) 
                   SELECT NULL,Entry_date, Expiry_date, '".$state."', ".$input_quantity.",Product_ID  
                   FROM entries WHERE Entry_ID = ".$entry_id.";";

        if (mysqli_multi_query($conn,$query)) {
            echo "<div class='page-text'>Succesfully partly $state</div>";
        }
        mysqli_close($conn);
    }
}

function db_update(){

    include('partials/config.php');
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if(isset($_POST['update'])){
        $query = "UPDATE entries 
              SET Entry_date = '".$_POST['entry-date']."',Expiry_date = '".$_POST['expiry-date']."',Quantity = '".$_POST['quantity']."',State = '".$_POST['state']."'
               WHERE Entry_ID = ".$_POST['update'].";";

        $message = "Verandering geslaagd, check de Voedselbeheer pagina";
    }
    else{
        $query = "DELETE FROM entries WHERE Entry_ID=".$_POST['delete'].";";
        $message = "Toegevoegd product is verwijderd, check de Voedselbeheer pagina";
    }

    $result = $conn->query($query);

    if ($result) {
        echo "<div class='page-text'>";
        echo $message;
        echo "</div>";
    }
    $conn->close();
}

function db_product(){
    include('partials/config.php');
    $conn = new mysqli($servername, $username, $password, $dbname);

    mysqli_set_charset($conn ,"utf8");
// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT products.Name, Entry_date,Entry_ID, Expiry_date, State, Quantity
              FROM entries
              JOIN products ON entries.Product_ID = products.Product_ID
              WHERE Entry_ID = ".$_GET['edit']."";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();

        echo "<div class='page-form'>";
            echo "<form method='post'>";
                echo "<div class='form-group row'>";
                    echo "<label class='col-sm-2 col-form-label'>Product naam</label>";
                    echo "<div class='col-sm-10'><input type='text' readonly class='form-control-plaintext' value='".$row['Name']."'></div>";
                echo "</div>";
                echo "<div class='form-group row'>";
                    echo "<label class='col-sm-2 col-form-label'>Hoeveelheid</label>";
                    echo "<div class='col-sm-10'><input type='text' name='quantity' class='form-control' value='".$row['Quantity']."'></div>";
                echo "</div>";
                echo "<div class='form-group row'>";
                    echo "<label class='col-sm-2 col-form-label'>Consumptie status</label>";
                    echo "<div class='col-sm-10'>";
                        echo "<select class='form-control' name='state'>";
                            echo "<option selected='selected'>".$row['State']."</option>";
                            echo "<option>inventory</option>";
                            echo "<option>consumed</option>";
                            echo "<option>disposed</option>";
                        echo "</select>";
                    echo "</div>";
                echo "</div>";
                echo "<div class='form-group row'>";
                    echo "<label class='col-sm-2 col-form-label'>Invoerdatum</label>";
                    echo "<div class='col-sm-10'><input type='date' name='entry-date' class='form-control' value='".date("Y-m-d", strtotime($row['Entry_date']))."'></div>";
                echo "</div>";
                echo "<div class='form-group row'>";
                    echo "<label class='col-sm-2 col-form-label'>Houdbaarheidsdatum</label>";
                    echo "<div class='col-sm-10'><input type='date' name='expiry-date' class='form-control' value='".date("Y-m-d", strtotime($row['Expiry_date']))."'></div>";
                echo "</div>";
                echo "<div class='form-group row'>";
                    echo "<div class='col-sm-2'><button class='btn btn-delete' title='Verwijderen' name='delete' value='".$row['Entry_ID']."' type='submit'>Verwijder</button></div>";
                    echo "<div class='col-sm-2'><button class='btn btn-green' title='Aanpassen' name='update' value='".$row['Entry_ID']."' type='submit'>Pas aan</button></div> ";
                echo "</div>";
            echo "</form>";
        echo "</div>";


        if(isset($_POST['update'])or isset($_POST['delete'])){db_update();}


    }
    else{
        echo "<div class='page-text'>Something went wrong</div>";
    }

    $conn->close();

}

function db_pie_chart_euro(){
    include('partials/config.php');
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT categories.Name, SUM(ROUND(products.price * Quantity,2)) as Total_value, categories.Color 
              FROM entries
              JOIN products ON entries.Product_ID = products.Product_ID 
              JOIN sub_categories ON products.Sub_category_ID = sub_categories.Sub_category_ID 
              JOIN categories ON sub_categories.Category_ID = categories.Category_ID
              GROUP BY categories.Name ORDER BY Total_value DESC";

    $result = $conn->query($query);

    $data = array();
    $categories = array();
    $values = array();
    $colors = array();
    $total = 0;

    if ($result->num_rows > 0) {

        while($row = $result->fetch_assoc()) {
            array_push($categories,$row['Name']);
            array_push($values,$row['Total_value']);
            array_push($colors,$row['Color']);
            $total += $row['Total_value'];
        }
        array_push($data, $categories, $colors, $values,$total);
    }
    else {
        echo "No data";
    }

    return $data;

    $conn->close();

}

function db_bar_chart($state){
    include('partials/config.php');
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if($state != NULL){
        $condition = "WHERE state = '".$state."'";
    }else{
        $condition = "";
    }

    $query = "SELECT MONTHNAME(Entry_date) as Month, YEAR(Entry_date) as Year, SUM(ROUND( products.Price * Quantity,2)) as Total_value 
              FROM `entries` JOIN products ON entries.Product_ID = products.Product_ID ".$condition." 
              GROUP BY MONTHNAME(Entry_date) ORDER BY MONTH(Entry_date), YEAR(Entry_date) LIMIT 12";

    $result = $conn->query($query);

    $data = array();
    $dates = array();
    $values = array();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $date = $row['Month'] . " ". strval($row['Year']);
            array_push($dates, $date);

            if($state == 'disposed'){
                array_push($values, $row['Total_value']*-1);
            }
            else{
                array_push($values, $row['Total_value']);
            }

        }
        array_push($data, $dates, $values);
    }
    else {
        echo "No data";
    }

    return $data;

    $conn->close();

}

function weight_converter($product_weight,$product_unit){

    $units = array('kilo','gram','stuks','None','milliliters','liters','centiliters');
    $dividers = array(1, 1000, 0, 0, 1000, 1, 100);

    foreach($units as $unit){
        if($unit == $product_unit){
            $key = array_search($unit, $units);
            $weight = intval($product_weight) / $dividers[$key];
        }
    }

    return $weight;
}

function db_weight_total($state){
    include('partials/config.php');
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if($state != NULL){
        $condition = "WHERE state = '".$state."'";
    }else{
        $condition = "";
    }

    $query = "SELECT Quantity, products.Amount, products.Unit
              FROM entries
              JOIN products ON entries.Product_ID = products.Product_ID ".$condition."";

    $result = $conn->query($query);

    $total = 0;

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $total += round(($row['Quantity']*weight_converter($row['Amount'],$row['Unit'])),2);
        }
    }
    else {
        echo "No data";
    }

    return $total;

    $conn->close();
}

function db_CO2_total(){
    include('partials/config.php');
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT Quantity, products.Amount, products.Unit, sub_categories.CO2
              FROM entries
              JOIN products ON entries.Product_ID = products.Product_ID 
              JOIN sub_categories ON products.Sub_category_ID = sub_categories.Sub_category_ID 
              JOIN categories ON sub_categories.Category_ID = categories.Category_ID";

    $result = $conn->query($query);

    $total = 0;

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $total += round(($row['Quantity']*weight_converter($row['Amount'],$row['Unit']))*$row['CO2'],2);
        }
    }
    else {
        echo "No data";
    }

    return $total;

    $conn->close();
}

function db_expired_products(){
    include('partials/config.php');
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT COUNT(state) as Total FROM `entries` WHERE CURRENT_DATE > Expiry_date AND state = 'inventory'";
    $result = $conn->query($query);
    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if($row['Total'] > 0){
                echo "<div class='page-text bg-danger' style='color:white;' <b>".$row['Total']. "</b> producten zijn over de datum <br>Beoordeel de kwaliteit en gebruik of gooi deze producten zo snel mogelijk weg via de <a href='".get_permalink( get_page_by_title( 'voedselbeheer' ) )."' style='color:white;text-decoration: underline'>Voedselbeheer</a> pagina</div>";
            }
            else{
                echo "<div class='page-text bg-success' style='color:white;'>Goed bezig! Geen enkel product is over de datum, zorg dat dit zo blijft door regelmatig de <a href='".get_permalink( get_page_by_title( 'voedselbeheer' ) )."' style='color:white;text-decoration: underline'>Voedselbeheer</a> pagina te checken</div>";
            }
        }
    }




    $conn->close();
}

function foodhold_register_assets()
{
    wp_register_script('bootstrap', get_template_directory_uri() . '/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js', array('jquery'));
    wp_register_script('charts', get_template_directory_uri() . '/node_modules/chart.js/dist/Chart.bundle.min.js', array('jquery'));
    wp_register_script('script', get_template_directory_uri() . '/js/script.js', array('jquery', 'bootstrap','charts'));
}

function foodhold_enqueue_assets()
{
    wp_enqueue_script('script');
    wp_enqueue_style('app', get_stylesheet_directory_uri().'/build/css/app.css',array(),'1.0','all');
}

add_action('init', 'foodhold_register_assets');
add_action('wp_enqueue_scripts', 'foodhold_enqueue_assets');

// include piece of code from that converts WordPress menu to Bootstrap Menu (Pattonwebz, 2018)
require_once( dirname( __FILE__ ) . '/php/class-wp-bootstrap-navwalker.php' );

//Register the location of the menu created in the WordPress client
register_nav_menus( array(
    'primary' => __( 'Primary Menu', 'foodhold' ),
) );

