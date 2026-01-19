<?php
session_start();
session_destroy();
header('Location: index.html'); // Change par ta page de connexion
exit;
