<footer>
    <section id="sitemap">
        <h2>Links</h2>
        <div class="footerNav">
            <nav class="navfooterbloc">
                <h3>Contacts</h3>
                <a href="info@emilie-foudelman.com">info@emilie-foudelman.com</a>
                <a href="tel:+41762416369">Tel : +41(0)762 41 63 69</a>
                <a aria-label="Chat on WhatsApp" href="https://wa.me/+32486100573"><span class="btnwhatsapp"><img alt="Chat on WhatsApp" src="<?=$repDeco?>/whatsappbtnwithtext.svg" /></span></a>
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