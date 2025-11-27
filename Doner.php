<?php
    // start php session to store user info
    session_start();

    // include database connection
    //mysqli connection to MySQL database
    include("config.php");


    // if login page is set up 
    /*

    // should redirect if not logged in
    if(!isset($_SESSION['user_id'])){
    
        header("Location: login.php") or whatever the php name
        exit();
    }

    $name =  $_SESSION['name'];
    $user_type =  $_SESSION['user_type'];
    */



    
    // mock session
    
    // initialize
    $name = "";
    
    // since no login page, use an existing user
    $_SESSION['user_id'] = 3;
    
    // initialize
    $user_type = "";

    // store user id in local var
    $user_id = $_SESSION['user_id'];

    // prepare SQL query to fetch the user's name and user type from the database
    $query = "SELECT name, user_type FROM users WHERE user_id = ? AND user_type = 'donor'";
    
    // prepare the SQL statement
    $stmt = mysqli_prepare($conn, $query);

    // bind the userid var to the sql statement
    mysqli_stmt_bind_param($stmt, "i", $user_id);

    // execute the prepared statement
    mysqli_stmt_execute($stmt);

    // bind the result columns to php var
    mysqli_stmt_bind_result($stmt, $name, $user_type);

    //fetch the row of the result
    mysqli_stmt_fetch($stmt);

    // close the prepared statement
    mysqli_stmt_close($stmt);

    // store retrieved name and user type in the session
    $_SESSION['name'] = $name;
    $_SESSION['user_type'] = $user_type;

    if($user_type !== 'donor'){
        echo "This page is for donors only.";
        exit();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>WNK</title>
</head>
<body class="donor">
    <div class="navigation">
        

        <a href="Doner.php">
            <img src="images/Frame3.png" alt="logo" class="wnk_logo">
        </a>

        <nav class="nav_link">

            <ul class="cust_options">
                <li>
                    <a href="#plateSection" class="plates">Browse Plates</a>
                </li>
                <li>
                    <a href="Donations.php" class="reserve">My Donations</a>
                </li>
            </ul>

        </nav>

        <div class="rightside">

            <img src="images/pfp.jpg" alt="profile" class="pfp">
            
            <p>
                <?php echo htmlspecialchars($name); ?>
            </p>

            <img src="images/menu_24dp_000000_FILL0_wght400_GRAD0_opsz24.svg" id="menuTogg" alt="menus_drop">

            <ul class="sidebar">
                <li>
                    <a href="CheckoutDonations.php" class="checkout">Checkout</a>
                </li>
                <li>
                    <a href="logout.php" class="sign_off">Sign Out</a>
                </li>
            </ul>

        </div>
    </div>
    
    <div class="heroSection">
        <div class="heroLeft">
            <h1 class="hero">
                Welcome, <?php echo htmlspecialchars($name); ?>!
            </h1>
        </div>

        <div class="heroRight">
            <h3 class="subHead">
                Discover fresh surplus plates from local restaurants at great prices.
                Browse today's selections, reserve your favorites, and help reduce food waste.
            </h3>
        
        </div>

        
    
    </div>
    
    <h1 id="plateSection">
        Available Plates
    </h1>

    <div class="platesGrid">
        <?php
        $plateQuery = "SELECT p.plate_id, p.description, p.cost, p.amt, r.restaurant_name, available_from, available_until, status
                        FROM plates p
                        JOIN restaurants r ON p.restaurant_id = r.restaurant_id
                        WHERE p.status = 'available'";
        $result = mysqli_query($conn, $plateQuery);
        
        while($plate = mysqli_fetch_assoc($result)):
        ?>
            <div class="plateCard">
                <h3><?= htmlspecialchars($plate['description']) ?></h3>
                <p class="restaurantName">Restaurant: <?= htmlspecialchars($plate['restaurant_name']) ?></p>
                <p>Price: $<?= number_format($plate['cost'], 2) ?></p>
                <p>Available: <?= intval($plate['amt']) ?></p>

                <form action="donate.php" method="POST">
                    <input type="hidden" name="plate_id" value="<?= $plate['plate_id'] ?>">

                    <label for="quantity_<?= $plate['plate_id'] ?>">Quantity:</label>
                    <input type="number"
                                id="quantity_<?= $plate['plate_id'] ?>"
                                name="quantity"
                                min="1"
                                max="<?= intval($plate['amt']) ?>"
                                value="1"
                                required>
                                
                    <button type="submit">Donate</button>
                </form>

            </div>

            <?php endwhile; ?>
    </div>

    <script src="menu.js"></script>
</body>
</html>