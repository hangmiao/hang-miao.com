<!DOCTYPE html>
<!--
Hang Miao

This is a php file with a style sheet to produce review pages for movies.
-->

<html>
    <head>
        <meta charset="UTF-8">
        <link href="movie.css" type="text/css" rel="stylesheet" />
        <title>Rancid Tomatoes</title>
        <link href="http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw3/images/rotten.gif" type="image/gif" rel="shortcut icon" />
    </head>
    <body>

        <?php
        $movie = $_REQUEST["film"];
        $infoArray = file("$movie/info.txt", FILE_IGNORE_NEW_LINES);
        $reviewArray = glob("$movie/review*.txt");
        $reviewNum = count($reviewArray);
        ?> 

        <div class="top_banner">
            <img src="http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw2/images/banner.png" alt="Rancid Tomatoes" />
        </div>
        <h1 class="top_title"><?= $infoArray[0] ?> (<?= $infoArray[1] ?>)</h1>
        <div class="overall_content">
            <div class="right_body">
                <div>
                    <img src="<?= $movie ?>/overview.png"
                         alt="general overview" />
                </div>
                <dl>
                    <?php
                    $overviews = file("$movie/overview.txt", FILE_IGNORE_NEW_LINES);
                    foreach ($overviews as $overview) {
                        $overviewArray = explode(":", $overview);
                        // process links
                        if ((preg_match("/:\/\//", $overview))) {
                            $overviewArray[1] = $overviewArray[1] . ':' . $overviewArray[2];
                            $overviewArray[2] = null;
                        }?>
                        <dt> <?php print "$overviewArray[0]"; ?></dt>
                        <dd> <?php print "$overviewArray[1]"; ?></dd>
                    <?php } ?>
                </dl>
            </div>

            <div class="left_top">
                <span class="rating">
                    <?php if ($infoArray[2] < 60) { ?>
                        <img src = "http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw2/images/rottenbig.png" alt = "Rotten" />
                    <?php } else {
                        ?>
                        <img src = "http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw3/images/freshbig.png" alt = "Fresh" />
                    <?php } ?>
                </span>
                <span class="rating"><?= $infoArray[2] ?>%</span>
            </div>

            <div class="reviews">
                <div class="review_columns">
                    <?php
                    for ($i = 0; $i < $reviewNum / 2; $i++) {
                        $reviewLinesArray = file("$reviewArray[$i]", FILE_IGNORE_NEW_LINES);
                        ?>
                        <p class="review_box">
                            <img src="http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw3/images/<?= strtolower($reviewLinesArray[1]) ?>.gif" 
                                 alt="<?= $reviewLinesArray[1] ?>" />
                            <q>
                                <?= $reviewLinesArray[0] ?>
                            </q>
                        </p>
                        <p class="personal_info">
                            <img src="http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw2/images/critic.gif" alt="Critic" />
                            <?= $reviewLinesArray[2] ?><br>
                            <span class="publication"> <?= $reviewLinesArray[3] ?></span>
                        </p>
                    <?php } ?>
                </div>

                <div class="review_columns">
                    <?php
                    for ($i = round($reviewNum / 2); $i < $reviewNum; $i++) {
                        $reviewLinesArray = file("$reviewArray[$i]", FILE_IGNORE_NEW_LINES);
                        ?>
                        <p class="review_box">
                            <img src="http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw3/images/<?= strtolower($reviewLinesArray[1]) ?>.gif" 
                                 alt="<?= $reviewLinesArray[1] ?>" />
                            <q>
                                <?= $reviewLinesArray[0] ?>
                            </q>
                        </p>
                        <p class="personal_info">
                            <img src="http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw2/images/critic.gif" alt="Critic" />
                            <?= $reviewLinesArray[2] ?><br>
                            <span class="publication"> <?= $reviewLinesArray[3] ?></span>
                        </p>
                    <?php } ?>
                </div>
            </div>
            <p class="left_bottom">
                (1-<?= $reviewNum ?>) of <?= $reviewNum ?>
            </p>
        </div>

        <div class="validators">
            <a href="http://validator.w3.org/check/referer">
                <img src="http://ws.mss.icics.ubc.ca/~cics516/cur/hw/hw1/images/w3c-html.png"
                     alt="Valid HTML5" />
            </a><br>
            <a href="http://jigsaw.w3.org/css-validator/check/referer?profile=css3">
                <img src="http://jigsaw.w3.org/css-validator/images/vcss"
                     alt="Valid CSS" />
            </a>
        </div>
    </body>
</html>