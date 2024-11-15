<?php
session_start();

// Session-Variablen löschen
session_unset();

// Die Session zerstören
session_destroy();

// Weiterleitung zur Login-Seite
header("Location: login.html");
exit();
