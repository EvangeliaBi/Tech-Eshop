<?php
session_start();    //Εδώ ξεκινά/επαναφέρει την τρέχουσα PHP session και καθιστά διαθέσιμη τη $_SESSION, καθώς πρέπει να κληθεί πριν από οποιαδήποτε έξοδο HTML ώστε να μην προκληθεί σφάλμα headers(cookie).
include 'config/db.php';    //Εισάγει το αρχείο που συνήθως ορίζει τη σύνδεση $conn στη βάση (MySQLi).

if ($_SERVER['REQUEST_METHOD'] == 'POST') {    //Eκτελεί το μπλοκ μόνο όταν η φόρμα υποβληθεί με μέθοδο POST. Αυτό αποτρέπει επεξεργασία σε GET αιτήματα.
    // Παρακάτω διαβάζονται οι τιμές από το POST και εφαρμόζεται η μέθοδος trim() για την αφαίρεση κενών στην αρχή/τέλος.
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password_input = trim($_POST['password']);

    //Βασική επικύρωση εισόδου στην εφαρμογή.
    if (empty($username) || empty($email) || empty($password_input)) {    // Εδώ γίνεται ο έλεγχος κενών ή μή ορισμένων κενών.
        $error = "Please fill in all fields.";      // Μήνυμα σφάλματος για συμπλήρωση όλων των πεδίων εάν έστω και μία συνθήκη είναι true και η άλλη false τότε βγαίνει true όλη η συνθήκη, παροτρύνοντας τον χρήστη να συμπληρώσει όλα τα πεδία στην φόρμα εγγραφής.
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {     // Έλεγχος για σύνταξη του email, δηλαδή εάν περιέχει το ενδεδειγμένο format για email (πχ. @), καθώς εάν δεν το περιέχει τότε ενημερώνει τον χρήστη για invalid email.
        $error = "Invalid email format.";
    } elseif (strlen($password_input) < 8) {    // Απαίτηση για ελάχιστο μήκος κωδικού, καθώς εάν δεν ικανοποιείται η συνθήκη αυτή τότε ενημερώνει τον χρήστη για το αποδεκτό μήκος κωδικού πρόσβασης.
        $error = "Password must be at least 8 characters.";
    } else {

        // Έλεγχος ύπαρξης username με prepared statements.
        $stmt = mysqli_prepare($conn, "SELECT id FROM customers WHERE username=?");    // Εδώ προετοιμάζεται και εκτελείται ένα query για να δούμε αν υπάρχει ήδη το username, καθώς μέσω του prepared statement αποτρέπεται SQL injection.
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);    //Εδώ αποθηκεύεται το αποτέλεσμα στο statement handle ώστε να μπορεί να χρησιμοποιηθεί το mysqli_stmt_num_rows χωρίς να έρθουν τα δεδομένα με get_result.

        // Παρακάτω γίνεται έλεγχος αν το query επέστρεψε γραμμές κι εάν το username υπάρχει ήδη εμφανίζοντας στον χρήστη το αντίστοιχο μήνυμα σφάλματος.
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "Username already exists!";
        } else {

            mysqli_stmt_close($stmt);   // Εδώ κλείνει το προηγούμενο statement και απελευθερώνονται οι πόροι.

            $password = password_hash($password_input, PASSWORD_DEFAULT);   //Δημιουργία ασφαλούς one‑way hash κωδικού πρόσβασης. 

            $stmt = mysqli_prepare($conn, "INSERT INTO customers (username, password, email) VALUES (?, ?, ?)");   //Προετοιμάζει το INSERT με τρία placeholders και δένει τις παραμέτρους ως strings.
            mysqli_stmt_bind_param($stmt, "sss", $username, $password, $email);

            //Αν το INSERT εκτελεστεί, αποθηκεύεται το username στη session για authenticated state, στέλνεται redirect και ο κώδικας τερματίζεται με exit.
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['username'] = $username;
                header("Location: home.php");
                exit;
            } else {
                $error = "Error creating account.";    // Γενικό μήνυμα σφάλματος σε περίπτωση αποτυχίας.
            }
        }

        mysqli_stmt_close($stmt);   //Εξασφαλίζει ότι το τελευταίο statement κλείνει.
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Sign Up - Tech E-Shop</title> <!--Τίτλος που εμφανίζεται στην καρτέλα του browser και στα αποτελέσματα μηχανών αναζήτησης.-->

    <link rel="icon" type="image/png" href="images/Tech E-Shop-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>

    <nav class="navbar navbar-dark bg-dark">
        <div class="container"> <!--Μέσα στο navbar περιορίζει το πλάτος και κεντράρει το περιεχόμενο σύμφωνα με το responsive grid.-->
            <a class="navbar-brand" href="home.php">Tech E-Shop</a> <!--Λογότυπο/όνομα που συνδέει στην αρχική σελίδα. -->
        </div>
    </nav>

    <div class="container mt-5"> <!--κύριο container με top margin (mt-5) για απόσταση από το navbar.-->
        <div class="row justify-content-center"> <!--Χρήση του Bootstrap grid για κεντράρισμα και περιορισμό του πλάτους της φόρμας σε μεσαίες/μεγάλες οθόνες.-->
            <div class="col-md-5">

                <div class="card shadow">
                    <div class="card-body">

                        <h3 class="text-center mb-3">Create Account</h3> <!--επικεφαλίδα κεντραρισμένη με κάτω περιθώριο.-->

                        <?php if (isset($error)): ?> <!--server‑side μπλοκ PHP που εμφανίζει μήνυμα σφάλματος, καθώς το μήνυμα τυπώνεται με htmlspecialchars($error) για αποφυγή XSS — μετατρέπει ειδικούς χαρακτήρες σε HTML entities.-->
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <form method="post"> <!--ξεκινά τη φόρμα που υποβάλλεται με POST στην ίδια URL (απουσία action). Η χρήση POST είναι σωστή για credentials γιατί δεν εμφανίζει δεδομένα στο URL.-->

                            <!-- Username -->
                            <div class="mb-3">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control"> <!--πεδίο κειμένου, όπου η μεταβλητή name είναι το κλειδί που θα διαβαστεί στο $_POST['username']. Η κλάση form-control εφαρμόζει Bootstrap styling.-->
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control"> <!--ενεργοποιεί browser validation για μορφή email και εμφανίζει κατάλληλο keyboard σε κινητές συσκευές. Η μεταβλητή name="email" επιτρέπει server‑side ανάγνωση.-->
                            </div>

                            <!-- Password -->
                            <div class="mb-2">
                                <label>Password</label>

                                <div class="input-group"> <!--το input και το toggle τοποθετούνται σε μία γραμμή με Bootstrap input group.-->
                                    <input type="password"
                                        name="password"
                                        id="password"
                                        class="form-control"> <!--πεδίο κωδικού που κρύβει χαρακτήρες. Το id="password" χρησιμοποιείται από το JS (signup.js και password.js)-->

                                    <span class="input-group-text toggle-password" style="cursor:pointer;"> <!--clickable περιοχή που περιέχει το εικονίδιο ματιού. Η κλάση toggle-password είναι ο selector που το JS θα δέσει για toggle ορατότητας. Το inline style="cursor:pointer;" κάνει το στοιχείο εμφανώς clickable-->
                                        <i class="bi bi-eye"></i> <!--το εικονίδιο ματιού από Bootstrap Icons. Το JS θα αλλάξει την κλάση σε bi-eye-slash όταν ο κωδικός είναι ορατός.-->
                                    </span>
                                </div>
                            </div>

                            <!-- Strength indicator -->
                            <p id="strength" class="small mb-3"></p> <!--Το id="strength" επιτρέπει στο signup.js να ενημερώνει κείμενο και χρώμα. Η κλάση small μειώνει το μέγεθος της γραμματοσειράς.-->

                            <button type="submit" class="btn btn-primary w-100"> <!--κουμπί υποβολής με Bootstrap styling, όπου το w-100 το κάνει full width μέσα στο column.-->
                                Sign Up
                            </button>

                        </form>

                        <p class="mt-3 text-center"> <!--βοηθητικό κείμενο με συνδέσμους για υπάρχοντες χρήστες και επιστροφή στην αρχική σελίδα.-->
                            Already have an account?
                            <a href="signin.php">Sign In</a>
                            <br>
                            <a href="home.php">Back to Home</a>
                        </p>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!--Εισαγωγή εξωτερικών αρχείων JS-->
    <script src="js/signup.js"></script> <!--φορτώνει το τοπικό αρχείο signup.js που υλοποιεί τον strength meter και τυχόν client‑side validation. Τοποθετείται στο τέλος του body ώστε να εκτελεστεί αφού το DOM έχει φορτωθεί.-->
    <script src="js/password.js"></script> <!--φορτώνει το τοπικό αρχείο που υλοποιεί το toggle ορατότητας του password (σύνδεση toggle-password με #password).-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script> <!--φορτώνει το JavaScript του Bootstrap. Τοποθετείται στο τέλος για να μην μπλοκάρει το initial rendering.-->

</body>

</html>