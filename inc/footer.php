<footer>
    <section id="sitemap">
        <h2 class="invisible-titre">Links</h2>
        <div class="footerNav">
            <nav class="navfooterbloc">
                <h3>Contacts</h3>
                <a class="maillink" href="mailto:info@emilie-foudelman.com">info@emilie-foudelman.com</a>
                <a class="phonelink" href="tel:+41762416369">Tel : +41 76 241 63 69</a>
                <a class="whatsapplink" aria-label="Chat on WhatsApp" href="https://wa.me/+32486100573"><span><img alt="Chat on WhatsApp" src="<?=$repDeco?>/whatsappbtnwithtext.svg" /></span></a>
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