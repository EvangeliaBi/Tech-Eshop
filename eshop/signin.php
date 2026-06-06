<?php
session_start();    // Ξεκινά ή επαναφέρει την τρέχουσα PHP session και καθιστά διαθέσιμη τη $_SESSION, καθώς πρέπει να κληθεί πριν από οποιαδήποτε έξοδο HTML. 
include 'config/db.php';    // Εισάγει το αρχείο σύνδεσης στη βάση ($conn), ώστε να χρησιμοποιηθεί η σύνδεση MySQLi στον υπόλοιπο κώδικα. 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {    // Ελέγχει ότι η φόρμα υποβλήθηκε με μέθοδο POST πριν επεξεργαστεί τα credentials.

    // Με την μέθοδο trim() αφαιρούνται περιττά κενά από την αρχή/τέλος για καθαριότητα εισόδου πριν την επαλήθευση.
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {    // Βασικός έλεγχος για κενά πεδία. Αν λείπει κάτι, θέτει μήνυμα σφάλματος για εμφάνιση στο UI.
        $error = "Please enter both username and password.";
    } else {
        // Εντός του μπλοκ κώδικα else προετοιμάζεται το ερώτημα με placeholder ? και δένει το $username ως string. Αυτό προστατεύει από SQL injection επειδή τα δεδομένα δεν ενσωματώνονται απευθείας στο query. 
        $stmt = mysqli_prepare($conn, "SELECT password FROM customers WHERE username=?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        // Παρακάτω παίρνει το αποτέλεσμα και φορτώνει την πρώτη γραμμή ως associative array.
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        // Παρακάτω συγκρίνεται το plain password με το hashed password που είναι αποθηκευμένο στη βάση. Αν ταιριάζει, αποθηκεύει το username στη session και κάνει redirect. Αν όχι, θέτει κατάλληλο μήνυμα σφάλματος. Η χρήση password_verify είναι η σωστή πρακτική για hashed passwords.
        if ($row) {

            if (!empty($row['password']) && password_verify($password, $row['password'])) {
                $_SESSION['username'] = $username;
                header("Location: home.php");
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "User does not exist.";
        }

        mysqli_stmt_close($stmt);   // Κλείνει το prepared statement και ολοκληρώνει την επεξεργασία POST.
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Sign In - Tech E-Shop</title> <!--ορίζει τον τίτλο που εμφανίζεται στην καρτέλα του browser και στα αποτελέσματα μηχανών αναζήτησης.-->

    <link rel="icon" type="image/png" href="images/Tech E-Shop-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="home.php">Tech E-Shop</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">

                <div class="card shadow">
                    <div class="card-body">

                        <h3 class="text-center mb-3">Sign In</h3>

                        <!--Παρακάτω το μπλοκ εμφανίζει το μήνυμα σφάλματος με htmlspecialchars() για αποφυγή XSS όταν εμφανίζουμε user‑controlled κείμενο-->
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars($error); ?> <!--εκτυπώνει το μήνυμα σφάλματος με htmlspecialchars για να αποφευχθεί XSS — μετατρέπει ειδικούς χαρακτήρες σε HTML entities. Αυτό είναι σημαντικό όταν το μήνυμα μπορεί να περιέχει δεδομένα από τον χρήστη.-->
                            </div>
                        <?php endif; ?>

                        <form method="post"> <!--Η φόρμα χρησιμοποιεί method="post" και πεδία name="username" / name="password" ώστε το PHP να τα διαβάσει από $_POST. Στην ουσία ξεκινάει τη φόρμα που υποβάλλει δεδομένα με POST. Η απουσία action σημαίνει ότι η φόρμα υποβάλλεται στην ίδια URL σελίδας. Η χρήση POST είναι σωστή για credentials επειδή δεν εμφανίζει τα δεδομένα στο URL.-->

                            <!-- Username -->
                            <div class="mb-3">
                                <label>Username</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <!--πεδίο κειμένου με όνομα username — αυτό το όνομα χρησιμοποιείται στο PHP ως $_POST['username']-->
                                    <input type="text"
                                        name="username"
                                        class="form-control"
                                        placeholder="Enter username">
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label>Password</label>

                                <div class="input-group">
                                    <!--πεδίο κωδικού. Το type="password" κρύβει τους χαρακτήρες.-->
                                    <input type="password"
                                        name="password"
                                        id="signin-password"
                                        class="form-control"
                                        placeholder="Enter password">

                                    <span class="input-group-text toggle-password-signin" style="cursor:pointer;"> <!--clickable περιοχή με εικονίδιο ματιού (bi-eye) που πιθανότατα ενεργοποιεί το js/password.js για να εμφανίσει/αποκρύψει τον κωδικό.-->
                                        <i class="bi bi-eye"></i>
                                    </span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100"> <!--κουμπί υποβολής με Bootstrap primary στυλ και w-100 που το κάνει full width μέσα στο container. Όταν πατηθεί, η φόρμα υποβάλλεται.-->
                                Sign In
                            </button>

                        </form>

                        <!--Παρακάτω παρέχονται επιλογές πλοήγησης για χρήστες που δεν έχουν λογαριασμό ή θέλουν να επιστρέψουν στην αρχική σελίδα.-->
                        <p class="mt-3 text-center">
                            Don't have an account?
                            <a href="signup.php">Sign Up</a>
                            <br>
                            <a href="home.php">Back to Home</a>
                        </p>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script> <!--Φορτώνει το JavaScript του Bootstrap (bundle περιλαμβάνει Popper). Τοποθετείται στο τέλος του body για να μην μπλοκάρει το rendering της σελίδας.-->
    <script src="js/password.js"></script> <!--Φορτώνει το τοπικό JavaScript αρχείο password.js. Από το markup καταλαβαίνουμε ότι αυτό το αρχείο υλοποιεί το toggle ορατότητας του password (σύνδεση με toggle-password-signin και signin-password).-->

</body>

</html>