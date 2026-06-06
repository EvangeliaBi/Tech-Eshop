<?php
session_start();   // Ξεκινά ή επαναφέρει την τρέχουσα session και κάνει διαθέσιμη την $_SESSION. Χρειάζεται πριν από οποιαδήποτε έξοδο HTML γιατί στέλνει/χρησιμοποιεί headers cookie.
include 'config/db.php';   // Φορτώνει το αρχείο με τη σύνδεση στη βάση δεδομένων. Η χρήση include επιτρέπει επαναχρησιμοποίηση της σύνδεσης.

// Δημιουργία cart αν δεν υπάρχει, καθώς ελέγχει αν υπάρχει το κλειδί cart στη session και αν όχι το αρχικοποιείται ως κενός πίνακας, εξασφαλίζοντας ότι οι επόμενες λειτουργίες [], array_search, unset δεν θα προκαλέσουν warnings.
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Προσθήκη προϊόντος στο καλάθι, όπου ελέγχεται αν το URL περιέχει παράμετρο add. Αυτό σημαίνει ότι η προσθήκη ενεργοποιείται μέσω GET request.
if (isset($_GET['add'])) {
    $product_id = (int)$_GET['add'];   // κάνει cast της τιμής σε ακέραιο για μείωση κινδύνου injection και για να εξασφαλιστεί ο τύπος. Το cast δεν αντικαθιστά την ανάγκη για server side validation ότι το id υπάρχει στη βάση.
    $_SESSION['cart'][] = $product_id;  // προσθέτει το id στο τέλος του πίνακα cart. Η δομή αυτή επιτρέπει πολλαπλές εμφανίσεις του ίδιου προϊόντος και δεν διατηρεί ξεχωριστό πεδίο quantity.
    $message = "Product added to cart!";   // αποθηκεύει μήνυμα επιβεβαίωσης για εμφάνιση στο UI. 
}

// Αφαίρεση προϊόντος από το καλάθι, όπου ελέγχεται αν υπάρχει παράμετρος remove στο URL. Η αφαίρεση γίνεται επίσης μέσω GET.
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];  // cast σε ακέραιο για ασφάλεια.
    if (($key = array_search($remove_id, $_SESSION['cart'])) !== false) {   // ψάχνει την πρώτη εμφάνιση του id στον πίνακα και ελέγχει σωστά την επιστροφή 0 έναντι false. Η χρήση !== false είναι σωστή για να μην συγχέεται το index 0 με μη εύρεση.
        unset($_SESSION['cart'][$key]);    // αφαιρεί το στοιχείο από τον πίνακα.
        $_SESSION['cart'] = array_values($_SESSION['cart']);    // Επανασυντάσσεται ο πίνακας ώστε τα κλειδιά να γίνουν συνεχή 0,1,2. Αυτό διευκολύνει την επανάληψη με foreach.
        $message = "Product removed from cart!";    // θέτει μήνυμα επιβεβαίωσης.
    }
}

if (isset($_POST['checkout'])) {    // Έλεγχος εάν στάλθηκε η φόρμα κι εάν πατήθηκε το κουμπί checkout με εκτέλεση του κώδικα μόνο όταν γίνει submit.

    if (empty($_SESSION['cart'])) {     // Ελέγχει αν δεν υπάρχουν προϊόντα στο καλάθι, διότι εάν ίναι άδειο τερματίζεται η διαδικασία και εμφανίζεται το αντίστοιχο μήνυμα.
        $message = "Your cart is empty!";
    } else {

        if (!isset($_POST['payment'])) {    // Έλεγχος επιλογής πληρωμής, ελέγχοντάς αν ο χρήστης δεν επέλεξε radio button και εμφανίζοντας το κατάλληλο μήνυμα.
            $message = "Please select a payment method!";
        } else {

            $payment = $_POST['payment'];   // Λαμβλανεται η επιλογή του χρήστη από την φόρμα εάν όλα είναι σωστά.

            $_SESSION['payment_method'] = $payment;    // Προσωρινή αποθήκευση στο session της μεθόδου πληρωμής από τον χρήστη για εμφάνιση στην σελίδα του checkout και για χρήση αυτού στο order summary.

            $_SESSION['cart'] = array();    // Άδειασμα καλαθιού μέσω της ολοκλήρωσης αγοράς.

            $message = "Order completed successfully using " . htmlspecialchars($payment) . "!";    // Εμφάνιση μηνύματος επιτυχίας στον χρήστη για την επιλογή του συγκεκριμένου τρόπου πληρωμής
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Shopping Cart - Tech E-Shop</title> <!--τίτλος που εμφανίζεται στην καρτέλα του browser.-->

    <link rel="icon" type="image/png" href="images/Tech E-Shop-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/cart.css">

</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark"> <!--δημιουργεί responsive navbar με σκούρο theme.-->
        <div class="container">
            <a class="navbar-brand" href="home.php">Tech E-Shop</a> <!--brand link που οδηγεί στην αρχική σελίδα.-->

            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['username'])): ?> <!--Εδώ ελέγχεται αν ο χρήστης είναι συνδεδεμένος.-->
                        <li class="nav-item">
                            <a class="nav-link">Hello, <?php echo $_SESSION['username']; ?></a> <!--Εμφάνιση προσωποποιημένου μηνύματος καλωσορίσματος.-->
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4"> <!--ξεκινά το κύριο container με top margin.-->

        <h1 class="mb-4">Your Shopping Cart</h1> <!--κύριος τίτλος σελίδας με κάτω περιθώριο.-->

        <?php if (isset($message)): ?> <!--ελέγχει αν υπάρχει μήνυμα για εμφάνιση.-->
            <div class="alert alert-success">
                <?php echo $message; ?> <!--εμφανίζει το μήνυμα σε alert.-->
            </div>
        <?php endif; ?>

        <!--EMPTY CART UI-->
        <?php if (empty($_SESSION['cart'])): ?> <!--Εδώ ελέγχεται αν το καλάθι είναι άδειο.-->

            <!--Παρακάτω το μπλοκ HTML που ακολουθεί δημιουργεί ένα κεντρικό card με εικονίδιο, τίτλο, περιγραφή και κουμπί Shop Now.-->
            <div class="d-flex justify-content-center align-items-center" style="min-height: 60vh;"> <!--εξασφαλίζει ότι το card είναι ορατό και κεντραρισμένο κάθετα.-->
                <div class="card text-center shadow p-4" style="max-width: 400px; width:100%;">

                    <div class="mb-3">
                        <i class="bi bi-cart-x" style="font-size: 60px; color:#6c757d;"></i>
                    </div>

                    <h4 class="mb-2">Your cart is empty</h4>

                    <p class="text-muted mb-3">
                        Looks like you haven’t added anything yet.
                    </p>

                    <a href="home.php" class="btn btn-primary">
                        <i class="bi bi-shop"></i> Shop Now
                    </a>

                </div>
            </div>

        <?php else: ?> <!--Εμφάνιση προιόντων όταν το καλάθι δεν είναι άδειο.-->

            <?php
            $total = 0; // αρχικοποιεί το συνολικό ποσό των προιόντων με 0.
            $stmt = mysqli_prepare($conn, "SELECT name, description, price, image FROM products WHERE id = ?");    //Προετοιμάζει prepared statement για ανάκτηση στοιχείων προϊόντος ανά id. Η χρήση prepared statement προστατεύει από SQL injection.
            ?>

            <!-- PRODUCTS -->
            <div class="list-group">

                <?php foreach ($_SESSION['cart'] as $item):     // Εδώ πραγματοποιείται επανάληψη για κάθε id προιόντος στο καλάθι.

                    mysqli_stmt_bind_param($stmt, "i", $item);    // μετατρέπει το id ως integer.
                    mysqli_stmt_execute($stmt);     // κι εδώ εκτελείται το statement.

                    $result = mysqli_stmt_get_result($stmt);    // εδώ λαμβάνεται το result set.
                    $product = mysqli_fetch_assoc($result);    // φέρνει την πρώτη γραμμή ως associative array.

                    if (!$product) continue;    // Εδώ ο κώδικας θα συνεχιστεί ακόμα κι εάν το item (το προιόν) δεν βρέθηκε στη βάση. 

                    $total += $product['price'];    // προσθέτει την τιμή στο συνολικό ποσό των προιόντων που προστέθηκαν στο καλάθι.
                ?>

                    <div class="list-group-item cart-row d-flex align-items-center justify-content-between mb-2 rounded-3 shadow-sm"> <!--ανοίγει ένα div που αντιπροσωπεύει μία γραμμή προϊόντος στη λίστα (list-group-item).-->

                        <div class="d-flex align-items-center"> <!--ενεργοποιεί flexbox, κεντράρει κάθετα τα στοιχεία και τα τοποθετεί σε αντίθετες άκρες (πληροφορίες αριστερά, τιμή/κουμπί δεξιά). Στην ουσία ξεκινά ένα flex container για την εικόνα και το κείμενο, ώστε να στοιχίζονται οριζόντια και να ευθυγραμμίζονται κάθετα.-->
                            <!--Παρακάτω το src συντίθεται με το όνομα αρχείου που επιστρέφει η βάση ($product['image']) μέσα στον φάκελο images/.-->
                            <img src="images/<?php echo $product['image']; ?>"
                                class="cart-img me-3"
                                alt="<?php echo $product['name']; ?>"> <!--εμφανίζει το όνομα προϊόντος ως εναλλακτικό κείμενο-->

                            <div>
                                <h6 class="mb-1 fw-bold">
                                    <?php echo $product['name']; ?> <!--εμφανίζει το όνομα του προιόντος-->
                                </h6>

                                <p class="product-desc mb-0">
                                    <?php echo $product['description']; ?> <!--εμφανίζει την τιμή του προιόντος-->
                                </p>
                            </div>
                        </div>

                        <div class="text-end"> <!--container που στοιχίζει το περιεχόμενο δεξιά (text aligned end), καθώς είναι κατάλληλο για τιμή και κουμπί.-->
                            <p class="fw-bold mb-2">
                                $<?php echo $product['price']; ?> <!--Εμφανίζει την τιμή του προιόντος με bold.-->
                            </p>

                            <!--Το παρακάτω link καλεί την ίδια την σελίδα (cart.php) με παράμετρο remove=<id> για να αφαιρεθεί το προϊόν.-->
                            <a href="cart.php?remove=<?php echo $item; ?>"
                                class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i> Remove
                            </a>
                        </div>

                    </div>

                <?php endforeach; ?> <!--κλείνει το foreach που επαναλάμβανε για κάθε $_SESSION['cart'] item.-->

            </div> <!--Εδώ κλείνει το container div της list-group που περιέχει όλες τις γραμμές προϊόντων.-->

            <?php mysqli_stmt_close($stmt); ?> <!--Εδώ κλείνει το prepared statement που χρησιμοποιήθηκε για την ανάκτηση προϊόντων, απελευθερώνοντας πόρους στον client/server της βάσης. Είναι καλή πρακτική να κλείνουν τα statements όταν δεν χρειάζονται πια.-->

            <!-- TOTAL -->
            <div class="card shadow p-3 mt-3 mb-4"> <!--εμφανίζει το συνολικό ποσό των προιόντων σε ξεχωριστό card με padding και σκιά.-->
                <h4>Total: $<?php echo $total; ?></h4> <!--Εμφανίζει το συνολικό άθροισμα των προιόντων που υπολογίστηκε κατά την εκτέλεση του επαναληπτικού βρόγχου.-->

                <form method="post" action="cart.php"> <!--Ορίζει ότι τα δεδομένα θα σταλούν με POST και το action="cart.php" σημαίνει ότι η φόρμα στέλνει τα δεδομένα στην ίδια σελίδα.-->

                    <h5 class="mb-3">Select Payment Method:</h5>
                    <!--Με το type="radio" → επιλέγεται μία μόνο επιλογή και το required υποχρεώνει τον χρήστη να επιλέξει κάτι πριν κάνει submit.-->
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment" value="credit card" required>
                        <label class="form-check-label">
                            Credit Card 💳
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment" value="paypal">
                        <label class="form-check-label">
                            PayPal 🅿️
                        </label>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="payment" value="cash">
                        <label class="form-check-label">
                            Cash on Delivery 💵
                        </label>
                    </div>

                    <button type="submit" name="checkout" class="btn btn-success w-100"> <!--Αποστολή φόρμας.-->
                        <i class="bi bi-bag-check"></i> Checkout
                    </button>

                </form>
            </div>

            <!-- CONTINUE SHOPPING -->
            <!--πλήρες‑πλάτους κουμπί που επιστρέφει τον χρήστη στην αρχική σελίδα/κατάστημα με όλα τα προιόντα από όλες τις κατηγορίες, όπου σύμφωνα με το UX τοποθετείται κάτω από το συνολικό ποσό ώστε ο χρήστης να μπορεί εύκολα να συνεχίσει τις αγορές.-->
            <a href="home.php"
                class="btn btn-secondary w-100 mb-5">
                <i class="bi bi-arrow-left"></i> Continue Shopping
            </a>

        <?php endif; ?> <!--κλείνει το if που ελέγχει αν το καλάθι είναι άδειο ή όχι-->

    </div> <!--Εδώ κλείνει το κύριο container της σελίδας.-->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script> <!--Εδώ φορτώνεται το Bootstrap JavaScript bundle, καθώς τοποθετείται στο τέλος του body για να μην μπλοκάρει το initial rendering.-->

</body>

</html>