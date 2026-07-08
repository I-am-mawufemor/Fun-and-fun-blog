    <?php
    $page_title = "Home | " . APP_NAME;
    require_once __DIR__ . "/../../include/header.php";
    require_once __DIR__ . "/../../include/navbar.php"
    ?>

      
   <!-- hero section-->

   <main>
    <article>

        <!-- HERO -->
        <section id="hero"
            style="background-image: url('<?php echo BASE_URL; ?>public/image/middle.JPG');">

            <!-- Texture grain overlay -->
            <div class="hero-grain"></div>
            <!-- Animated green underline -->
            <div class="hero-line"></div>

            <div class="container">

                <p class="section-subtitle">
                    <span>Welcome to Fun &amp; Tech</span>
                </p>

                <h2 class="hero-title">
                    Corporate Entrepreneurship <strong>Through Action</strong>
                </h2>

                <p class="hero-text">
                    Having the perspective to see an opportunity and the talent to create value from that opportunity.
                </p>

                <button class="btn">Explore</button>

            </div>
        </section>

    </article>
</main>

<!-- FEATURES -->
<section class="section features">
    <div class="container">

        <ul class="features-list">

            <li class="features-item">
                <div class="item-icon">
                    <ion-icon name="school-outline"></ion-icon>
                </div>
                <div>
                    <h3>Skill Development</h3>
                    <p>Gain real-world entrepreneurial skills.</p>
                </div>
            </li>

            <li class="features-item">
                <div class="item-icon">
                    <ion-icon name="people-outline"></ion-icon>
                </div>
                <div>
                    <h3>Global Network</h3>
                    <p>Connect with students worldwide.</p>
                </div>
            </li>

            <li class="features-item">
                <div class="item-icon">
                    <ion-icon name="leaf-outline"></ion-icon>
                </div>
                <div>
                    <h3>Leadership</h3>
                    <p>Develop leadership skills through teamwork.</p>
                </div>
            </li>

            <li class="features-item">
                <div class="item-icon">
                    <ion-icon name="trophy-outline"></ion-icon>
                </div>
                <div>
                    <h3>Competitions</h3>
                    <p>Participate in national & global contests.</p>
                </div>
            </li>

        </ul>

    </div>
</section>


    <?php 
require_once __DIR__ . "/../../include/footer.php"
;?>