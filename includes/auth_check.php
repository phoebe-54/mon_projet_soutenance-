<?php
// Acces public autorise : les pages restent compatibles avec l'ancien include.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
