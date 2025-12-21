<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin Entry Point
|--------------------------------------------------------------------------
| Single responsibility:
| - Redirect users to the admin login page
| - Avoid duplicate UI / logic
|--------------------------------------------------------------------------
*/

header('Location: /backend/admin/login.php');
exit;
