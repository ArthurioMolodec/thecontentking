<?php

if (isset($_SESSION['access_token'])) {
    $name = "Admin";
} else {
    $name = "";
}

?>

<nav class="navbar is-flex is-primary is-justify-content-space-between" style="align-items: center;" role="navigation" aria-label="main navigation">

    <div class="navbar-brand">
        <a class="navbar-item pr-0" href="<?php echo webUrl; ?>">
            <img class="mb-1" src="/assets/logo.png" style="display: block; max-width:60px;max-height:50px;width: auto;height: auto;">
            <h3 class="m-3">TheContentKing</h3>
        </a>
       
    </div>
    <?php echo isset($_SESSION['premium_user']) && $_SESSION['premium_user'] ?  '<img class="mb-1" src="/assets/premium.png" style="margin-right: 5px;margin-left:auto; display: block;max-width:60px;max-height:50px;width: auto;height: auto;">' : ''; ?>

    <?php

    echo isset($_SESSION['id_token']) && $_SESSION['id_token'] ?

        '<a class="navbar-item logout-btn pl-0 pr-1" href="logout">
        Logout
    </a>'

        :

        ""

    ?>

    <!-- prestonzen.com -->

</nav>
