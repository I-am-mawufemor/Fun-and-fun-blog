    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="logo">
            <a href="?page=home">
                <img class="image-logo"
                src="<?php echo BASE_URL; ?>public/image/logo.jpg" alt="Tech & Fun Logo">
            </a>
           Tech & Fun
        </div>
        <div class="navbar-center">
            <select aria-label="Course category">
                <option>All Course</option>
                <option>Design</option>
                <option>Development</option>
                <option>Business</option>
            </select>
            <input class="nav-search" type="text" placeholder="Search for course..." />
        </div>

        <div class="nav-right">
            <button class="icon-btn" aria-label="Profile">
                <?php if (!isLoggedIn()): ?>
                    <i class="fa-regular fa-user"></i>
                <?php else: ?>
                    <?php
                    $fullName  = $_SESSION['full_name'] ?? $_SESSION['user_name'] ?? 'User';
                    $nameParts = explode(' ', trim($fullName));
                    $initials  = strtoupper($nameParts[0][0] ?? '');
                    if (count($nameParts) > 1) {
                        $initials .= strtoupper(end($nameParts)[0] ?? '');
                    }
                    ?>
                    <span class="user-initials"><?= htmlspecialchars($initials) ?></span>
                <?php endif; ?>
            </button>
            <button class="icon-btn" aria-label="Wishlist">
                <i class="fa-regular fa-heart"></i>
            </button>
            <!-- Trigger Button (outside the sidenav) -->
            <button class="menu-btn" aria-label="Menu">
                Menu <i class="fa-solid fa-bars"></i>
            </button>

            <!-- Sidenav -->
            <div id="mySidenav" class="sidenav">
                <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
                <a href="#">About</a>
                <a href="#">Services</a>
                <a href="?page=blog">Blog</a>
                <a href="?page=teachers-pack">Teacher Toolkit</a>
                <?php if (isLoggedIn()): ?>
                    <a href="?page=logout">Logout</a>
                <?php endif; ?>

                <?php if (!isLoggedIn()): ?>
                    <a class="sign-in" href="?page=login">Sign In</a>
                     <a class="sign-in" href="?page=register">Sign Up</a>
                <?php endif; ?>
            </div>
            <!-- Add this just after the sidenav div -->
            <div class="sidenav-overlay" onclick="closeNav()"></div>
        </div>

    </nav>