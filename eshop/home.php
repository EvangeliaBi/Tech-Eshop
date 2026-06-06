<?php
session_start();    // ξεκινά ή επαναφέρει την τρέχουσα session και κάνει διαθέσιμη τη $_SESSION. Στέλνει/χρησιμοποιεί headers (cookie) οπότε πρέπει να κληθεί πριν από οποιαδήποτε έξοδο HTML.
require_once 'config/db.php';   // Εδώ φορτώνεται η σύνδεση με την βάση. Η χρήση require_once διασφαλίζει ότι το αρχείο φορτώνεται μία φορά και ότι, αν λείπει, η εκτέλεση σταματά με fatal error. Στην ουσία εισάγεται το αρχείο που ορίζει τη σύνδεση στη βάση (συνήθως $conn). Η χρήση require_once διασφαλίζει ότι το αρχείο φορτώνεται μία φορά και ότι αν λείπει θα σταματήσει η εκτέλεση με fatal error.

// Φίλτρο κατηγορίας και προετοιμασία query.
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;   // Ελέγχει αν υπάρχει η παράμετρος category στο query string. Αν υπάρχει, την κάνει cast σε ακέραιο (int) για ασφάλεια (αποφυγή injection/μη αναμενόμενων τιμών). Αν όχι, θέτει 0 που σημαίνει «όλες οι κατηγορίες».

if ($category_id > 0) { //Εδώ ελέγχεται αν πρέπει να εφαρμοστεί φίλτρο κατηγορίας.
    //Παρακάτω δημιουργείται το prepared statement με placeholder ?. Τα prepared statements προετοιμάζουν το template SQL στον server και προστατεύουν από SQL injection όταν δεθούν οι παράμετροι. 
    $stmt = mysqli_prepare($conn, "
        SELECT id, name, description, price, image 
        FROM products 
        WHERE category_id = ?
    ");
    mysqli_stmt_bind_param($stmt, "i", $category_id);   //Δένει την παράμετρο $category_id στο placeholder ως integer ("i"). Αυτό εξασφαλίζει ότι η τιμή μεταβιβάζεται με σωστό τύπο και δεν ενσωματώνεται απευθείας στο SQL.
} else {    // Εναλλακτικά προετοιμάζεται statement χωρίς WHERE για να επιστραφούν όλα τα προϊόντα και τότε εκτελείται το μπλοκ κώδκα της else.
    $stmt = mysqli_prepare($conn, "
        SELECT id, name, description, price, image 
        FROM products
    ");
}

mysqli_stmt_execute($stmt); //Εκτελεί το prepared statement στον MySQL server. Η εκτέλεση στέλνει τις δεσμευμένες παραμέτρους και τρέχει το query.
$result = mysqli_stmt_get_result($stmt);   //Παίρνει το result set ως αντικείμενο mysqli_result που υποστηρίζει συναρτήσεις όπως mysqli_fetch_assoc, από την στιγμή που αυτή η συνάρτηση απαιτεί το mysqlnd driver και επιστρέφει false για queries που δεν παράγουν result set. 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tech E-Shop</title> <!--Ο τίτλος που εμφανίζεται στην καρτέλα του browser και στα αποτελέσματα αναζήτησης.-->

    <link rel="icon" type="image/png" href="images/Tech E-Shop-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"> <!--Φορτώνει το Bootstrap CSS από CDN για grid, components και utilities. Τοποθετείται στο <head> ώστε τα στυλ να εφαρμόζονται πριν το rendering.-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/home.css">
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark"> <!--Δημιουργεί responsive navbar-->
        <div class="container"> <!--περιορίζει πλάτος και κεντράρει περιεχόμενο.-->
            <a class="navbar-brand" href="home.php"> <!--Brand link με μικρό λογότυπο — καλό για αναγνωρισιμότητα και πρόσβαση στην αρχική σελίδα.-->
                <img src="images/Tech E-Shop-logo.png" width="35">
                Tech E-Shop
            </a>

            <div class="collapse navbar-collapse"> <!--Το collapse επιτρέπει το responsive collapse σε μικρές οθόνες· ms-auto ωθεί τα nav items δεξιά.-->
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['username'])): ?> <!--Η συνθήκη isset($_SESSION['username']) ελέγχει αν υπάρχει και δεν είναι null το στοιχείο username μέσα στη $_SESSION. Αυτό σημαίνει ότι «αν ο χρήστης είναι συνδεδεμένος (έχει username στη session), τότε εκτέλεσε το HTML που ακολουθεί». Η χρήση isset αποφεύγει warnings όταν το index δεν υπάρχει.-->
                        <li class="nav-item">
                            <a class="nav-link">Hello, <?php echo $_SESSION['username']; ?></a> <!--μέσα στο anchor (<a>) υπάρχει στατικό κείμενο "Hello, " και στη συνέχεια ενσωματώνεται δυναμικά το περιεχόμενο της $_SESSION['username']. Η echo τυπώνει την τιμή στον HTML output.-->
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">Cart <i class="bi bi-cart"></i></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="signin.php">Sign In</a></li>
                        <li class="nav-item"><a class="nav-link" href="signup.php">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!--HERO SECTION -->
    <div class="container mt-5 text-center">

        <h1 class="fw-bold mb-3">Find Your Perfect Computer</h1>

        <p class="lead">
            Laptops • Gaming PCs • Workstations
        </p>

        <p class="text-muted mb-4">
            High performance technology for students, professionals and gamers.
        </p>

        <a href="#products" class="btn btn-dark btn-lg"> <!--CTA button που οδηγεί στο anchor #products-->
            <i class="bi bi-cpu"></i> Explore Products
        </a>

    </div>

    <!--FEATURES -->
    <div class="container text-center mt-5">
        <div class="row">

            <div class="col-md-4">
                <i class="bi bi-lightning-charge fs-2"></i>
                <h6 class="mt-2">High Performance</h6>
                <p class="text-muted small">Fast and reliable devices</p>
            </div>

            <div class="col-md-4">
                <i class="bi bi-shield-check fs-2"></i>
                <h6 class="mt-2">Trusted Quality</h6>
                <p class="text-muted small">Top brands and components</p>
            </div>

            <div class="col-md-4">
                <i class="bi bi-cash-stack fs-2"></i>
                <h6 class="mt-2">Best Prices</h6>
                <p class="text-muted small">Affordable for every budget</p>
            </div>

        </div>
    </div>

    <!-- PRODUCTS -->
    <div class="container mt-5" id="products"> <!--νοίγει container Bootstrap με top margin (mt-5) και id="products" (anchor για το CTA). Το container περιορίζει και κεντράρει το περιεχόμενο.-->

        <h2 class="text-center mb-4">Our Products</h2>

        <!-- CATEGORY BUTTONS -->
        <div class="category-bar"> <!-- container για τα κουμπιά κατηγοριών.-->

            <a href="home.php"
                class="cat-btn <?php if ($category_id == 0) echo 'active'; ?>"> <!--anchor για την επιλογή All. Η inline PHP προσθέτει την κλάση active όταν δεν έχει επιλεγεί κατηγορία (δηλαδή category_id == 0).-->
                All
            </a>

            <?php
            $cat_stmt = mysqli_prepare($conn, "SELECT id, name FROM categories");   // προετοιμάζει prepared statement για να πάρει id και name από τον πίνακα categories. 
            mysqli_stmt_execute($cat_stmt);     // εκτελεί το prepared statement.
            $cat_result = mysqli_stmt_get_result($cat_stmt);    // παίρνει το result set ώστε να γίνει fetch με mysqli_fetch_assoc.

            while ($cat = mysqli_fetch_assoc($cat_result)):    // loop που διατρέχει κάθε γραμμή (κατηγορία). 
            ?>
                <!--Παρακάτω για κάθε κατηγορία δημιουργείται anchor που περνάει category=<id> στο URL. Η inline PHP προσθέτει active όταν η τρέχουσα κατηγορία είναι επιλεγμένη.-->
                <a href="home.php?category=<?php echo $cat['id']; ?>"
                    class="cat-btn <?php if ($category_id == $cat['id']) echo 'active'; ?>">
                    <?php echo $cat['name']; ?> <!--Εμφάνιση ονόματος κατηγορίας.-->
                </a>
            <?php endwhile; ?> <!--κλείνει το while loop των κατηγοριών.-->

        </div>

        <!-- PRODUCTS GRID -->
        <div class="row">

            <?php while ($row = mysqli_fetch_assoc($result)): ?> <!--PHP loop που διατρέχει το result set των προϊόντων (το $result προήλθε από προηγούμενο query). Για κάθε προϊόν θα παραχθεί ένα column με κάρτα.-->

                <div class="col-lg-3 col-md-4 col-sm-6 mb-4"> <!--responsive column: 4 στήλες σε large (lg), 3 σε medium (md), 2 σε small (sm). mb-4 προσθέτει κάτω περιθώριο.-->

                    <div class="card h-100 shadow-sm product-card">

                        <!--εμφανίζει εικόνα προϊόντος. Το src συντίθεται από το όνομα αρχείου που επιστρέφει η DB.-->
                        <img src="images/<?php echo $row['image']; ?>"
                            class="card-img-top product-img">

                        <div class="card-body d-flex flex-column">

                            <h5 class="fw-bold">
                                <?php echo $row['name']; ?> <!--εμφανίζει το όνομα προϊόντος.-->
                            </h5>

                            <p class="text-muted"> <!--εμφανίζει σύντομη περιγραφή κόβοντας στους 80 χαρακτήρες με την μέθοδο substr.-->
                                <?php echo substr($row['description'], 0, 80); ?>...
                            </p>

                            <h6 class="text-primary fw-bold">
                                $<?php echo $row['price']; ?> <!--εμφανίζει την τιμή του προϊόντος.-->
                            </h6>

                            <?php if (isset($_SESSION['username'])): ?> <!--ελέγχει αν ο χρήστης είναι συνδεδεμένος. Αν ναι, εμφανίζει κουμπί προσθήκης στο καλάθι.-->
                                <!--Παρακάτω το link αυτό καλεί την cart.php με query parameter add=<product id>. Το mt-auto ωθεί το κουμπί στο κάτω μέρος της κάρτας (flex layout).-->
                                <a href="cart.php?add=<?php echo $row['id']; ?>"
                                    class="btn btn-primary mt-auto">
                                    <i class="bi bi-cart-plus"></i> Add to Cart     <!--εικονίδιο και κείμενο κουμπιού.-->
                                </a>
                            <?php else: ?>  <!--αν ο χρήστης δεν είναι συνδεδεμένος, εμφανίζεται εναλλακτικό κουμπί που οδηγεί στη σελίδα σύνδεσης-->
                                <a href="signin.php"
                                    class="btn btn-outline-secondary mt-auto">
                                    Sign in to Buy  <!--προτρέπει τον χρήστη να συνδεθεί για να αγοράσει.-->
                                </a>
                            <?php endif; ?>

                        </div>

                    </div>
                </div>

            <?php endwhile; ?>     <!--Εδώ κλείνει το loop των προϊόντων.-->

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>    <!--φορτώνει το Bootstrap JS-->

</body>

</html>