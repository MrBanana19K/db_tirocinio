<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 20/01/18
 * Time: 20.01
 */

?>

<nav class="navbar is-info">
    <div class="navbar-brand">
        <a href="index.php" class="title navbar-item">
            <?= SITE_NAME ?>
        </a>
    </div>

    <div class="navbar-end">
        <div class="navbar-item has-dropdown is-hoverable">
            <a class="navbar-link">
                <?= $user["email"] ?>
            </a>

            <div class="navbar-dropdown">
                <div class="navbar-item">
                    <p class="title is-4 is-capitalized">
                        <span class="icon">
                            <img src="<?= $user["picture"] ?>">
                        </span>
                        <span>
                            <?= $user["name"] ?>
                        </span>
                    </p>
                </div>
                <div class="navbar-item is-pulled-right">
                    <a class="button" href="utils/logout.php">
                        <span>Esci</span>
                        <span class="icon">
                            <i class="fa fa-sign-out" aria-hidden="true"></i>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>