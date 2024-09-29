<footer>
    <section id="sitemap">
        <h2>Links</h2>
        <div class="footerNav">

            <nav class="navfooterbloc">
                <h3>Contacts</h3>
                <a href="info@walk.brussels">info@emilie-foudelman.com</a>
                <a href="tel:+32486100573">+32(0)486 10 05 73</a>
                <address>Dans un grand chalet dansla montagne<br> Suisse</address>
            </nav>
            <nav class="navfooterbloc">
                <h3>Menu</h3>
                <?php echo $menuMain_view ?>
            </nav>
        </div>
    </section>
    <nav id="menuRS" class="nav-rs">
        <?php
        foreach ($menuRS as $item) {
            echo "<a href=" . $item->page . " title='" . $item->titre . "' target='_blank'><div class='rs " . $item->titre . "'></div></a>";
        }
        ?>
    </nav>
</footer>