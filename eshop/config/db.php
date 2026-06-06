<?php
$host = "127.0.0.1";   // Διεύθυνση του MySQL server. Εδώ χρησιμοποιείται η τοπική διεύθυνση IPv4 127.0.0.1 αντί για το localhost.
$user = "root";     // Όνομα χρήστη MySQL, που είναι ο root και είναι ο προεπιλεγμένος διαχειριστής
$password = "";     // Κενό string δίχως κωδικό πρόσβασης.
$db = "eshop";      // Όνομα της βάσης δεδομένων που θα επιλεγεί ως προεπιλεγμένη μετά τη σύνδεση.
$port = 3307;       // Η θύρα/πόρτα που ακούει ο server.

// Δημιουργία σύνδεσης, όπου καλείται η συνάρτηση mysqli_connect με παραμέτρους: host, username, password, database, port. Σε επιτυχία επιστρέφει ένα connection object (ή resource/object ανάλογα με την έκδοση), σε αποτυχία επιστρέφει false.
$conn = mysqli_connect($host, $user, $password, $db, $port);

// Παρακάτω ελέγχεται αν η σύνδεση απέτυχε (δηλαδή αν η mysqli_connect επέστρεψε false), τερματίζεται ο κώδικας και εκτυπώνεται το μήνυμα. Εδώ συνδυάζεται με mysqli_connect_error() για να εμφανίσει το κείμενο του σφάλματος που επέστρεψε η MySQL.
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
