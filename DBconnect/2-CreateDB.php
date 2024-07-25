<?php
require_once '1-ConnectDB.php' ;


// DataBase name
$DataBaseName= 'Bakery' ;

// query to create DataBase
$query = "CREATE DATABASE IF NOT EXISTS $DataBaseName COLLATE utf8mb4_bin ";
if(mysqli_query($connect,$query)){
    echo "Database created successfully\n";
} else {
    die("Error creating database: " . mysqli_error($connect));
}

// Select the database

mysqli_select_db($connect, $DataBaseName);


// SQL query to create the Admin
$queryCreateTableAdmin = 
"CREATE TABLE IF NOT EXISTS Admin (
admin_name VARCHAR(50) NOT NULL PRIMARY KEY,
password VARCHAR(255) NOT NULL)";



// Execute the query
if (mysqli_query($connect, $queryCreateTableAdmin)) {
    echo "Table 'Admin' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}




// SQL query to create the User
$queryCreateTableUser = 
"CREATE TABLE IF NOT EXISTS User (
user_id INT AUTO_INCREMENT PRIMARY KEY,
user_name VARCHAR(50) NOT NULL,
phone_number VARCHAR(50) NOT NULL,
email VARCHAR(255) UNIQUE NOT NULL,
password VARCHAR(255) NOT NULL,
address TEXT NOT NULL)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableUser)) {
    echo "Table 'User' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}


// SQL query to create the Feedback
$queryCreateTableFeedback = 
"CREATE TABLE IF NOT EXISTS Feedback (
feedback_id INT AUTO_INCREMENT PRIMARY KEY,
text TEXT NOT NULL,
date datetime NOT NULL DEFAULT current_timestamp(),

user_id INT NOT NULL,
FOREIGN KEY (user_id) REFERENCES User (user_id),
admin_name VARCHAR(50) NOT NULL DEFAULT 'root',
FOREIGN KEY (admin_name) REFERENCES Admin (admin_name)
)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableFeedback)) {
    echo "Table 'Feedback' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}



// order_state > on card > ordered > reject ||   accept > ready 
// SQL query to create the OrderList
$queryCreateTableOrderList = 
"CREATE TABLE IF NOT EXISTS OrderList (
order_id INT AUTO_INCREMENT PRIMARY KEY,
order_date datetime NULL,
order_state VARCHAR(50) NOT NULL,
total_order_price VARCHAR(50) NULL,
user_id INT NOT NULL,
FOREIGN KEY (user_id) REFERENCES User (user_id)
)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableOrderList)) {
    echo "Table 'OrderList' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}







// SQL query to create the Conut
$queryCreateTableConut = 
"CREATE TABLE IF NOT EXISTS Conut (
conut_name VARCHAR(50) PRIMARY KEY,
description TEXT NOT NULL,
image MEDIUMBLOB NOT NULL,
price VARCHAR(50) NOT NULL,
admin_name VARCHAR(50) NOT NULL,
FOREIGN KEY (admin_name) REFERENCES Admin (admin_name)
)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableConut)) {
    echo "Table 'Conut' created successfully\n";
} else {
    die("Error creating table 'Conut': " . mysqli_error($connect));
}


// SQL query to create the Chimney
$queryCreateTableChimney = 
"CREATE TABLE IF NOT EXISTS Chimney (
chimney_name VARCHAR(50) PRIMARY KEY,
description VARCHAR(255) NOT NULL,
image MEDIUMBLOB  NOT NULL,
price VARCHAR(50) NOT NULL,
admin_name VARCHAR(50) NOT NULL,
FOREIGN KEY (admin_name) REFERENCES Admin(admin_name))";

// Execute the query
if (mysqli_query($connect, $queryCreateTableChimney)) {
    echo "Table 'Chimney' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}


// SQL query to create the Drink
$queryCreateTableDrink = 
"CREATE TABLE IF NOT EXISTS Drink (
drink_name VARCHAR(50) PRIMARY KEY,
image MEDIUMBLOB  NOT NULL,
price VARCHAR(50) NOT NULL,
admin_name VARCHAR(50) NOT NULL,
FOREIGN KEY (admin_name) REFERENCES Admin(admin_name))";

// Execute the query
if (mysqli_query($connect, $queryCreateTableDrink)) {
    echo "Table 'Drink' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}







// SQL query to create the Spread
$queryCreateTableSpread = 
"CREATE TABLE IF NOT EXISTS Spread (
spread_name VARCHAR(50) PRIMARY KEY,
price VARCHAR(50) NOT NULL,
admin_name VARCHAR(50) NOT NULL,
FOREIGN KEY (admin_name) REFERENCES Admin(admin_name))";

// Execute the query
if (mysqli_query($connect, $queryCreateTableSpread)) {
    echo "Table 'Spread' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}



// SQL query to create the Topping
$queryCreateTableTopping = 
"CREATE TABLE IF NOT EXISTS Topping (
topping_name VARCHAR(50) PRIMARY KEY,
price VARCHAR(50) NOT NULL,
admin_name VARCHAR(50) NOT NULL,
FOREIGN KEY (admin_name) REFERENCES Admin(admin_name))";

// Execute the query
if (mysqli_query($connect, $queryCreateTableTopping)) {
    echo "Table 'Topping' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}


// --------------------------------- Conut ------------------------------------
// SQL query to create the ConutAdditive
$queryCreateTableConutAdditive = 
"CREATE TABLE IF NOT EXISTS ConutAdditive (
conut_additive_id INT AUTO_INCREMENT PRIMARY KEY,
conut_name VARCHAR(50) NOT NULL,
FOREIGN KEY (conut_name) REFERENCES Conut(conut_name)
)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableConutAdditive)) {
    echo "Table 'ConutAdditive' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}



// SQL query to create the SpreadAdditiveCount
$queryCreateTableSpreadAdditiveCount = 
"CREATE TABLE IF NOT EXISTS SpreadAdditiveCount (
conut_additive_id INT NOT NULL,
spread_name VARCHAR(50) NULL,
quantity INT NULL,
FOREIGN KEY (conut_additive_id) REFERENCES ConutAdditive(conut_additive_id),
FOREIGN KEY (spread_name) REFERENCES Spread (spread_name)

)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableSpreadAdditiveCount)) {
    echo "Table 'SpreadAdditiveCount' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}


// SQL query to create the ToppingAdditiveCount
$queryCreateTableToppingAdditiveCount = 
"CREATE TABLE IF NOT EXISTS ToppingAdditiveCount (
conut_additive_id INT NOT NULL,
topping_name VARCHAR(50) NULL,
quantity INT NULL,
FOREIGN KEY (conut_additive_id) REFERENCES ConutAdditive(conut_additive_id),
FOREIGN KEY (topping_name) REFERENCES Topping (topping_name)

)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableToppingAdditiveCount)) {
    echo "Table 'ToppingAdditiveCount' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}


// --------------------------------- Chimney ------------------------------------

// SQL query to create the ChimneyAdditive
$queryCreateTableChimneyAdditive = 
"CREATE TABLE IF NOT EXISTS ChimneyAdditive (
chimney_additive_id INT AUTO_INCREMENT PRIMARY KEY,
chimney_name VARCHAR(50) NOT NULL,
FOREIGN KEY (chimney_name) REFERENCES Chimney(chimney_name) 
)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableChimneyAdditive)) {
    echo "Table 'ChimneyAdditive' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}




// SQL query to create the SpreadAdditiveChimney
$queryCreateTableSpreadAdditiveChimney = 
"CREATE TABLE IF NOT EXISTS SpreadAdditiveChimney (
chimney_additive_id INT NOT NULL,
spread_name VARCHAR(50) NULL,
quantity INT NULL,
FOREIGN KEY (chimney_additive_id) REFERENCES ChimneyAdditive(chimney_additive_id),
FOREIGN KEY (spread_name) REFERENCES Spread (spread_name)

)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableSpreadAdditiveChimney)) {
    echo "Table 'SpreadAdditiveChimney' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}


// SQL query to create the ToppingAdditiveChimney
$queryCreateTableToppingAdditiveChimney = 
"CREATE TABLE IF NOT EXISTS ToppingAdditiveChimney (
chimney_additive_id INT NOT NULL,
topping_name VARCHAR(50) NULL,
quantity INT NULL,
FOREIGN KEY (chimney_additive_id) REFERENCES ChimneyAdditive(chimney_additive_id),
FOREIGN KEY (topping_name) REFERENCES Topping (topping_name)

)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableToppingAdditiveChimney)) {
    echo "Table 'ToppingAdditiveChimney' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}


// --------------------------------- Drink ------------------------------------

// SQL query to create the DrinkAdditive
$queryCreateTableDrinkAdditive = 
"CREATE TABLE IF NOT EXISTS DrinkAdditive (
drink_additive_id INT AUTO_INCREMENT PRIMARY KEY,
drink_name VARCHAR(50) NOT NULL, 
FOREIGN KEY (drink_name) REFERENCES Drink(drink_name) 
)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableDrinkAdditive)) {
    echo "Table 'DrinkAdditive' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}


// SQL query to create the ToppingAdditiveDrink
$queryCreateTableToppingAdditiveDrink = 
"CREATE TABLE IF NOT EXISTS ToppingAdditiveDrink (
drink_additive_id INT NOT NULL,
topping_name VARCHAR(50) NULL,
quantity INT NULL,
FOREIGN KEY (drink_additive_id) REFERENCES DrinkAdditive(drink_additive_id),
FOREIGN KEY (topping_name) REFERENCES Topping (topping_name)

)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableToppingAdditiveDrink)) {
    echo "Table 'ToppingAdditiveDrink' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}



// -----------------------------------Container-----------------------

// SQL query to create the ConutContainer
$queryCreateTableConutContainer = 
"CREATE TABLE IF NOT EXISTS ConutContainer (
    order_id INT NOT NULL,
    conut_additive_id INT NOT NULL, 
    quantity INT NOT NULL, 
    unit_price VARCHAR(50) NULL,
    total_price VARCHAR(50) NULL, 
    FOREIGN KEY (order_id) REFERENCES OrderList (order_id),
    FOREIGN KEY (conut_additive_id) REFERENCES ConutAdditive(conut_additive_id)
)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableConutContainer)) {
    echo "Table 'ConutContainer' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}




// SQL query to create the ChimneyContainer
$queryCreateTableChimneyContainer = 
"CREATE TABLE IF NOT EXISTS ChimneyContainer (
    order_id INT NOT NULL,
    chimney_additive_id INT NOT NULL, 
    quantity INT NOT NULL, 
    unit_price VARCHAR(50) NULL,
    total_price VARCHAR(50) NULL, 
    FOREIGN KEY (order_id) REFERENCES OrderList (order_id),
    FOREIGN KEY (chimney_additive_id) REFERENCES ChimneyAdditive(chimney_additive_id)
)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableChimneyContainer)) {
    echo "Table 'ChimneyContainer' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}




// SQL query to create the DrinkContainer
$queryCreateTableDrinkContainer = 
"CREATE TABLE IF NOT EXISTS DrinkContainer (
    order_id INT NOT NULL,
    drink_additive_id INT NOT NULL, 
    quantity INT NOT NULL, 
    unit_price VARCHAR(50) NULL,
    total_price VARCHAR(50) NULL, 
    FOREIGN KEY (order_id) REFERENCES OrderList (order_id),
    FOREIGN KEY (drink_additive_id) REFERENCES DrinkAdditive(drink_additive_id)
)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableDrinkContainer)) {
    echo "Table 'DrinkContainer' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}

// --------------------------Offer------------------------------------

// SQL query to create the Offer table
$queryCreateTableOffer = 
"CREATE TABLE IF NOT EXISTS Offer (
    offer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    discounted_value DECIMAL(5,2) NOT NULL,
    offer_state VARCHAR(50) DEFAULT 'inactive'
)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableOffer)) {
    echo "Table 'Offer' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}

// SQL query to create the HappyHourOffer table
$queryCreateTableHappyHourOffer = 
"CREATE TABLE IF NOT EXISTS HappyHourOffer (
    offer_id INT NOT NULL,
    start_time TIME,
    end_time TIME,
    PRIMARY KEY (offer_id),
    FOREIGN KEY (offer_id) REFERENCES Offer(offer_id)
)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableHappyHourOffer)) {
    echo "Table 'HappyHourOffer' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}

// SQL query to create the SeasonalOffer table
$queryCreateTableSeasonalOffer = 
"CREATE TABLE IF NOT EXISTS SeasonalOffer (
    offer_id INT NOT NULL,
    fromDate DATE,
    toDate DATE,
    PRIMARY KEY (offer_id),
    FOREIGN KEY (offer_id) REFERENCES Offer(offer_id)
)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableSeasonalOffer)) {
    echo "Table 'SeasonalOffer' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}

// SQL query to create the ConutOfferContainer table
$queryCreateTableConutOfferContainer = 
"CREATE TABLE IF NOT EXISTS ConutOfferContainer (
    conut_name VARCHAR(50) NOT NULL,
    offer_id INT NOT NULL,
    PRIMARY KEY (conut_name, offer_id),
    FOREIGN KEY (conut_name) REFERENCES Conut(conut_name),
    FOREIGN KEY (offer_id) REFERENCES Offer(offer_id)
)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableConutOfferContainer)) {
    echo "Table 'ConutOfferContainer' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}

// SQL query to create the ChimneyOfferContainer table
$queryCreateTableChimneyOfferContainer = 
"CREATE TABLE IF NOT EXISTS ChimneyOfferContainer (
    chimney_name VARCHAR(50) NOT NULL,
    offer_id INT NOT NULL,
    PRIMARY KEY (chimney_name, offer_id),
    FOREIGN KEY (chimney_name) REFERENCES Chimney(chimney_name),
    FOREIGN KEY (offer_id) REFERENCES Offer(offer_id)
)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableChimneyOfferContainer)) {
    echo "Table 'ChimneyOfferContainer' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}

// SQL query to create the DrinkOfferContainer table
$queryCreateTableDrinkOfferContainer = 
"CREATE TABLE IF NOT EXISTS DrinkOfferContainer (
    drink_name VARCHAR(50) NOT NULL,
    offer_id INT NOT NULL,
    PRIMARY KEY (drink_name, offer_id),
    FOREIGN KEY (drink_name) REFERENCES Drink(drink_name),
    FOREIGN KEY (offer_id) REFERENCES Offer(offer_id)
)";

// Execute the query
if (mysqli_query($connect, $queryCreateTableDrinkOfferContainer)) {
    echo "Table 'DrinkOfferContainer' created successfully\n";
} else {
    die("Error creating table: " . mysqli_error($connect));
}


// --------------------------- Handling the automated update -----------------------

// SQL query to create the updatePrices() procedure
$queryCreateUpdatePricesProcedure = 
"CREATE PROCEDURE updatePrices(IN offer_id_var INT)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE conut_name_var VARCHAR(50);
    DECLARE chimney_name_var VARCHAR(50);
    DECLARE drink_name_var VARCHAR(50);
    DECLARE discount_value_var DECIMAL(5,2);
    DECLARE conut_cursor CURSOR FOR SELECT conut_name FROM conutoffercontainer WHERE offer_id = offer_id_var;
    DECLARE chimney_cursor CURSOR FOR SELECT chimney_name FROM chimneyoffercontainer WHERE offer_id = offer_id_var;
    DECLARE drink_cursor CURSOR FOR SELECT drink_name FROM drinkoffercontainer WHERE offer_id = offer_id_var;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    SELECT discounted_value INTO discount_value_var FROM offer WHERE offer_id = offer_id_var;

    OPEN conut_cursor;

    read_loop: LOOP
        FETCH conut_cursor INTO conut_name_var;

        IF done THEN
            LEAVE read_loop;
        END IF;

        UPDATE conut SET price = price - discount_value_var WHERE conut_name = conut_name_var;
    END LOOP;

    CLOSE conut_cursor;

    SET done = FALSE;

    OPEN chimney_cursor;

    read_loop: LOOP
        FETCH chimney_cursor INTO chimney_name_var;

        IF done THEN
            LEAVE read_loop;
        END IF;

        UPDATE chimney SET price = price - discount_value_var WHERE chimney_name = chimney_name_var;
    END LOOP;

    CLOSE chimney_cursor;

    SET done = FALSE;

    OPEN drink_cursor;

    read_loop: LOOP
        FETCH drink_cursor INTO drink_name_var;

        IF done THEN
            LEAVE read_loop;
        END IF;

        UPDATE drink SET price = price - discount_value_var WHERE drink_name = drink_name_var;
    END LOOP;

    CLOSE drink_cursor;
END;
";

// Execute the query
if (mysqli_query($connect, $queryCreateUpdatePricesProcedure)) {
    echo "Procedure 'updatePrices()' created successfully\n";
} else {
    die("Error creating procedure: " . mysqli_error($connect));
}

// SQL query to create the updateOfferState() procedure
$queryCreateUpdateOfferStateProcedure =
"CREATE PROCEDURE updateOfferState(IN offer_id_var INT)
BEGIN
    UPDATE offer SET offer_state = 'active' WHERE offer_id = offer_id_var;
END;
";

// Execute the query
if (mysqli_query($connect, $queryCreateUpdateOfferStateProcedure)) {
    echo "Procedure 'updateOfferState()' created successfully\n";
} else {
    die("Error creating procedure: " . mysqli_error($connect));
}

// SQL query to create the returnPricesToDefault() procedure
$queryCreateReturnPricesToDefaultProcedure = 
"CREATE PROCEDURE returnPricesToDefault(IN offer_id_var INT)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE conut_name_var VARCHAR(50);
    DECLARE chimney_name_var VARCHAR(50);
    DECLARE drink_name_var VARCHAR(50);
    DECLARE discount_value_var DECIMAL(5,2);
    DECLARE conut_cursor CURSOR FOR SELECT conut_name FROM conutoffercontainer WHERE offer_id = offer_id_var;
    DECLARE chimney_cursor CURSOR FOR SELECT chimney_name FROM chimneyoffercontainer WHERE offer_id = offer_id_var;
    DECLARE drink_cursor CURSOR FOR SELECT drink_name FROM drinkoffercontainer WHERE offer_id = offer_id_var;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    SELECT discounted_value INTO discount_value_var FROM offer WHERE offer_id = offer_id_var;

    OPEN conut_cursor;

    read_loop: LOOP
        FETCH conut_cursor INTO conut_name_var;

        IF done THEN
            LEAVE read_loop;
        END IF;

        UPDATE conut SET price = price + discount_value_var WHERE conut_name = conut_name_var;
    END LOOP;

    CLOSE conut_cursor;

    SET done = FALSE;

    OPEN chimney_cursor;

    read_loop: LOOP
        FETCH chimney_cursor INTO chimney_name_var;

        IF done THEN
            LEAVE read_loop;
        END IF;

        UPDATE chimney SET price = price + discount_value_var WHERE chimney_name = chimney_name_var;
    END LOOP;

    CLOSE chimney_cursor;

    SET done = FALSE;

    OPEN drink_cursor;

    read_loop: LOOP
        FETCH drink_cursor INTO drink_name_var;

        IF done THEN
            LEAVE read_loop;
        END IF;

        UPDATE drink SET price = price + discount_value_var WHERE drink_name = drink_name_var;
    END LOOP;

    CLOSE drink_cursor;
END;
";

// Execute the query
if (mysqli_query($connect, $queryCreateReturnPricesToDefaultProcedure)) {
    echo "Procedure 'returnPricesToDefault()' created successfully\n";
} else {
    die("Error creating procedure: " . mysqli_error($connect));
}

// SQL query to create the updateOfferStateInActive() procedure
$queryCreateUpdateOfferStateInActiveProcedure =
"CREATE PROCEDURE updateOfferStateInActive(IN offer_id_var INT)
BEGIN
    UPDATE offer SET offer_state = 'inactive' WHERE offer_id = offer_id_var;
END;
";

// Execute the query
if (mysqli_query($connect, $queryCreateUpdateOfferStateInActiveProcedure)) {
    echo "Procedure 'updateOfferStateInActive()' created successfully\n";
} else {
    die("Error creating procedure: " . mysqli_error($connect));
}

// SQL query to create the checkStartTime() procedure
$queryCreateCheckStartTimeProcedure =
"CREATE PROCEDURE checkStartAndEndTime()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE offer_id_var INT;
    DECLARE activate_cursor CURSOR FOR SELECT offer_id FROM happyhouroffer WHERE start_time < NOW();
    DECLARE deactivate_cursor CURSOR FOR SELECT offer_id FROM happyhouroffer WHERE end_time < NOW();
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN activate_cursor;

    read_loop: LOOP
        FETCH activate_cursor INTO offer_id_var;

        IF done THEN
            LEAVE read_loop;
        END IF;

        CALL updatePrices(offer_id_var);
        CALL updateOfferState(offer_id_var);
        
        UPDATE happyhouroffer SET start_time = NULL WHERE offer_id = offer_id_var;

    END LOOP;

    CLOSE activate_cursor;

    SET done = FALSE;

    OPEN deactivate_cursor;

    read_loop: LOOP
        FETCH deactivate_cursor INTO offer_id_var;

        IF done THEN
            LEAVE read_loop;
        END IF;

        CALL returnPricesToDefault(offer_id_var);
        CALL updateOfferStateInActive(offer_id_var);

        UPDATE happyhouroffer SET end_time = NULL WHERE offer_id = offer_id_var;

    END LOOP;

    CLOSE deactivate_cursor;
END;
";

// Execute the query
if (mysqli_query($connect, $queryCreateCheckStartTimeProcedure)) {
    echo "Procedure 'checkStartAndEndTime()' created successfully\n";
} else {
    die("Error creating procedure: " . mysqli_error($connect));
}



// SQL query to create the check_start_time_event event
$queryCreateEventEveryMinute = 
"CREATE EVENT check_start_and_end_time_event
ON SCHEDULE EVERY 1 MINUTE
STARTS '2024-01-01 00:00:00'
DO CALL checkStartAndEndTime();
";

// Execute the query
if (mysqli_query($connect, $queryCreateEventEveryMinute)) {
    echo "Event 'check_start_time_and_end_time_event' created successfully\n";
} else {
    die("Error creating event: " . mysqli_error($connect));
}


// SQL query to create the checkStartAndEndDate() procedure
$queryCreateCheckStartAndEndDateProcedure =
"CREATE PROCEDURE checkStartAndEndDate()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE offer_id_var INT;
    DECLARE activate_cursor CURSOR FOR SELECT offer_id FROM seasonaloffer WHERE fromDate <= CURDATE();
    DECLARE deactivate_cursor CURSOR FOR SELECT offer_id FROM seasonaloffer WHERE toDate < CURDATE();
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN activate_cursor;

    read_loop: LOOP
        FETCH activate_cursor INTO offer_id_var;

        IF done THEN
            LEAVE read_loop;
        END IF;

        CALL updatePrices(offer_id_var);
        CALL updateOfferState(offer_id_var);
    END LOOP;

    CLOSE activate_cursor;

    SET done = FALSE;

    OPEN deactivate_cursor;

    read_loop: LOOP
        FETCH deactivate_cursor INTO offer_id_var;

        IF done THEN
            LEAVE read_loop;
        END IF;

        CALL returnPricesToDefault(offer_id_var);
        CALL updateOfferStateInActive(offer_id_var);
    END LOOP;

    CLOSE deactivate_cursor;
END;
";

// Execute the query
if (mysqli_query($connect, $queryCreateCheckStartAndEndDateProcedure)) {
    echo "Procedure 'checkStartAndEndDate()' created successfully\n";
} else {
    die("Error creating procedure: " . mysqli_error($connect));
}

// SQL query to create the check_start_and_end_date_event event
$queryCreateEventEveryDay = 
"CREATE EVENT check_start_and_end_date_event
ON SCHEDULE EVERY 1 DAY
STARTS '2024-01-01 00:00:00'
DO CALL checkStartAndEndDate();
";

// Execute the query
if (mysqli_query($connect, $queryCreateEventEveryDay)) {
    echo "Event 'check_start_and_end_date_event' created successfully\n";
} else {
    die("Error creating event: " . mysqli_error($connect));
}


