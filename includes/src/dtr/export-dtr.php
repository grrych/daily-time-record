<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export'])) {
    redirect(BASE_URL . '/includes/reports/dtr.php');
}
