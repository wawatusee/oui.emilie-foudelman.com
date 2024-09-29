<header>
    <div class="mainTitleBlock">
        <h1><a href="https://www.emilie-foudelman.com/" target=”_blank”><img class="logo" src='<?=$repMedias.'/deco/logo_blanc.svg'?>' alt="lien vers le site emilie-foudelman.com"></a></h1>
        <div class="mainsubtitle"><?php echo $titleWebSite[0]?><?php echo " ".$titleWebSite[1]?><?php echo " ".$titleWebSite[2]?>
        <div class="menulangues">
        <?php //Liste déroulante des langues
        echo '<form method="get">';
        echo '<select name="lang" id="lang" onchange="this.form.submit()">';
        foreach ($langues_disponibles as $code_langue => $nom_langue) {
            echo '<option value="' . $code_langue . '"';
            if ($lang === $code_langue) {
                echo ' selected';
            }
            echo '>' . $code_langue . '</option>';
        }
        echo '</select>';
        echo '</form>';
        //Fin liste déroulante des langues?>
        </div>
    </div>
    </div>
    <div class="menu">
        <?php require_once "../inc/nav.php"?>
    </div>
</header>