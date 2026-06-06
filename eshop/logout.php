<?php
session_start();    // Ενεργοποίηση και έναρξη του session.

// Αντικαθιστά τον πίνακα $_SESSION με κενό array, δηλαδή αφαιρεί όλες τις session μεταβλητές από το τρέχον script, καθώς αυτό δεν διαγράφει τα δεδομένα στον server αν δεν κληθεί το session_destroy() μετά.
$_SESSION = array();

// Διαγραφή session cookie
if (ini_get("session.use_cookies")) {   // Έλεγχος εάν η session ID προωθείται μέσω cookie.
    $params = session_get_cookie_params();  // Παίρνει τα τρέχοντα attributes του cookie (path, domain, secure, httponly) ώστε να το διαγράψουμε με τα ίδια attributes.
    setcookie(      // Στέλνει ένα cookie με όνομα session_name() και παλιό expiry (time() - 42000) για να το διαγράψει στον browser. Η χρήση των ίδιων path/domain/flags εξασφαλίζει ότι το cookie θα αντικατασταθεί/διαγραφεί σωστά. 
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Καταστροφή session στον server.
session_destroy();     // Καταστρέφει τα δεδομένα που είναι αποθηκευμένα στον server για την τρέχουσα session. Δεν απενεργοποιεί αυτόματα τις global μεταβλητές $_SESSION ούτε διαγράφει το cookie — γι’ αυτό προηγούνται τα βήματα που καθαρίζουν $_SESSION και διαγράφουν το cookie.

// Redirect
header("Location: home.php");   // Στέλνει HTTP redirect στον browser, καθώς πρέπει να εκτελεστεί πριν από οποιαδήποτε έξοδο.
exit;   // Διασφαλίζει ότι ο κώδικας σταματά αμέσως μετά την αποστολή του header, αποφεύγοντας επιπλέον επεξεργασία ή έξοδο που θα μπορούσε να σπάσει το redirect. 
