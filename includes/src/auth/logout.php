<?php

if (isset($_SESSION['intern_id'])) {
    $_SESSION = [];
    session_destroy();
}