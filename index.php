<?php
include "ValidationLib.php";
$valid = new ValidationLib();
$errorBlock = '';

//Input values
$titleV = '';
$descV = '';
$authorV = '';
$linkV = '';
$pubDV = '';

//Input classes
$titleInput = '';
$descInput = '';
$authorInput = '';
$linkInput = '';

//Populates feed with rss file if no file found
if (simplexml_load_file("rss.rss") == '') {
    basicRSS();
    header("Refresh:0");
} else {
    $rss = simplexml_load_file("rss.rss");
    $mainTitle = $rss->channel->chTitle;
    $mainDesc = $rss->channel->chDescription;
}

function displayArticles($xml)
{
    $count = 0;
    foreach ($xml->channel->item as $x) {
        if ($count < 5) {
            echo "<fieldset><legend><a href='" . $x->link . "/" . $x->guid . "' target='blank'>" . $x->title . "</a></legend>" .
                $x->description . "<br/><br/><hr/>" .
                "<div class='pushRight'>" . $x->author . "<br>" . $x->pubDate . "</div></fieldset>";
            $count++;
        }
    }
}

function add($doc, $channel, $titleVal, $descVal, $authVal, $pubDVal, $linkVal, $guidVal)
{
    //Create Item Elements
    $item = $doc->createElement("item");
    $title = $doc->createElement("title", $titleVal);
    $desc = $doc->createElement("description", $descVal);
    $author = $doc->createElement("author", $authVal);
    $pubDate = $doc->createElement("pubDate", $pubDVal);
    $link = $doc->createElement("link", $linkVal);
    $guid = $doc->createElement("guid", $guidVal);

    //Append Item Elements
    $item->appendChild($title);
    $item->appendChild($desc);
    $item->appendChild($author);
    $item->appendChild($pubDate);
    $item->appendChild($link);
    $item->appendChild($guid);
    $channel->appendChild($item);
}

function populate()
{
    $doc = new DOMDocument('1.0', 'utf-8');
    $doc->preserveWhiteSpace = false;
    $doc->formatOutput = true;

    //Create Root
    $root = $doc->createElement("rss"); //Root Element Channel
    $version = $doc->createAttribute("version");
    $version->value = "2.0";
    $root->appendChild($version);

    //Create Channel Elements
    $channel = $doc->createElement("channel");
    $chTitle = $doc->createElement("chTitle", "My Feed");
    $chDesc = $doc->createElement("chDescription", "Top 5 Articles");
    $chLink = $doc->createElement("chLink", "test");

    //Append Channel Elements
    $channel->appendChild($chTitle);
    $channel->appendChild($chDesc);
    $channel->appendChild($chLink);

    //Append new item
    if ($_POST["pubDate"] == '') {
        $dateDefault = date("Y-m-d");
    } else {
        $dateDefault = $_POST["pubDate"];
    }
    add($doc, $channel, $_POST["title"], $_POST["description"], $_POST["author"], $dateDefault . " : " . date("h:i:sa"), $_POST["link"], guid());

    //Repopulate with existing items
    $rss = simplexml_load_file("rss.rss");
    $items = $rss->channel->item;
    $count = 1;
    foreach ($items as $i) {
        if ($count < 5) {
            add($doc, $channel, $i->title, $i->description, $i->author, $i->pubDate, $i->link, $i->guid);
            $count++;
        }
    }

    $root->appendChild($channel);
    $doc->appendChild($root);

    //Save RSS File
    $doc->save("rss.rss");
}

function sample()
{
    $doc = new DOMDocument('1.0', 'utf-8');
    $doc->preserveWhiteSpace = false;
    $doc->formatOutput = true;

    //Create Root
    $root = $doc->createElement("rss"); //Root Element Channel
    $version = $doc->createAttribute("version");
    $version->value = "2.0";
    $root->appendChild($version);

    //Create Channel Elements
    $channel = $doc->createElement("channel");
    $chTitle = $doc->createElement("chTitle", "My Feed");
    $chDesc = $doc->createElement("chDescription", "Top 5 Articles");
    $chLink = $doc->createElement("chLink", "test");

    //Append Channel Elements
    $channel->appendChild($chTitle);
    $channel->appendChild($chDesc);
    $channel->appendChild($chLink);

    //Append new item
    for ($i = 1; $i <= 10; $i++) {
        add($doc, $channel, "Article " . $i, "This is a description..", "Author", date("Y-m-d : h:i:sa"), "LinkstoArticle", guid());
    }

    $root->appendChild($channel);
    $doc->appendChild($root);

    //Save RSS File
    $doc->save("rss.rss");
}

function basicRSS()
{
    $doc = new DOMDocument('1.0', 'utf-8');
    $doc->preserveWhiteSpace = false;
    $doc->formatOutput = true;

    //Create Root
    $root = $doc->createElement("rss"); //Root Element Channel
    $version = $doc->createAttribute("version");
    $version->value = "2.0";
    $root->appendChild($version);

    //Create Channel Elements
    $channel = $doc->createElement("channel");
    $chTitle = $doc->createElement("chTitle", "My Feed");
    $chDesc = $doc->createElement("chDescription", "Top 5 Articles");
    $chLink = $doc->createElement("chLink", "test");

    //Append Channel Elements
    $channel->appendChild($chTitle);
    $channel->appendChild($chDesc);
    $channel->appendChild($chLink);

    $root->appendChild($channel);
    $doc->appendChild($root);

    //Save RSS File
    $doc->save("rss.rss");
}

function randLetter()
{
    $int = rand(0, 35);
    $pool = str_shuffle("abcdefghijklmnopqrstuvwxyz1234567890");
    $char = $pool[$int];
    return $char;
}

function guid()
{
    $code = '';
    for ($i = 0; $i < 32; $i++) {
        if ($i == 8 | $i == 12 | $i == 16 | $i == 20) {
            $code .= "-";
        }
        $code .= randLetter();
    }
    return $code;
}

if (isset($_POST["submit"])) {
    //Validate Title
    if ($valid->isEmpty($_POST["title"])) {
        $errorBlock .= "<li>Please provide an article title</li>";
        $titleInput = "errorInput";
    } else if ($valid->tooLong($_POST["title"], 100)) {
        $errorBlock .= "<li>Article title cannot be longer than 100 characters</li>";
        $titleInput = "errorInput";
    }

    //Validate Description
    if ($valid->isEmpty($_POST["description"])) {
        $errorBlock .= "<li>Please provide an article description</li>";
        $descInput = "errorInput";
    } else if ($valid->tooLong($_POST["description"], 1000)) {
        $errorBlock .= "<li>Article description cannot be longer than 1000 characters</li>";
        $descInput = "errorInput";
    }

    //Validate Author
    if ($valid->isEmpty($_POST["author"])) {
        $errorBlock .= "<li>Please provide an article author</li>";
        $authorInput = "errorInput";
    } else if ($valid->tooLong($_POST["author"], 50)) {
        $errorBlock .= "<li>Article author cannot be longer than 50 characters</li>";
        $authorInput = "errorInput";
    }

    //Validate Link
    if ($valid->isEmpty($_POST["link"])) {
        $errorBlock .= "<li>Please provide an article link</li>";
        $linkInput = "errorInput";
    } else if ($valid->tooLong($_POST["link"], 100)) {
        $errorBlock .= "<li>Article link cannot be longer than 100 characters</li>";
        $linkInput = "errorInput";
    }

    if ($valid->pass()) {
        populate();
        header("Refresh:0");
    } else {
        $titleV = $_POST["title"];
        $descV = $_POST["description"];
        $authorV = $_POST["author"];
        $linkV = $_POST["link"];
        $pubDV = $_POST["pubDate"];
    }
}

if (isset($_POST["sample"])) {
    sample();
    header("Refresh:0");
}

?>
<!DOCTYPE html>
<html>
<head lang="en">
    <title>RSS FEED</title>
    <meta charset="utf-8"/>
    <link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
<div id="wrap">
    <h1><?= htmlspecialchars($mainTitle) ?></h1>
    <div class="floatLeft">
        <h2><?= htmlspecialchars($mainDesc) ?></h2>
        <?php displayArticles($rss) ?>
    </div>
    <div class="floatRight">
        <h2>Add a New Article</h2>
        <form method="post" action="">
            <div>
                <div class="lblPad"><label for="title">Title:</label></div>
                <input type="text" id="title" name="title" class="<?= htmlspecialchars($titleInput) ?>"
                       value="<?= htmlspecialchars($titleV) ?>"/>
            </div>
            <div>
                <div class="lblPad  lbl"><label for="description">Description:</label></div>
                <textarea id="description" name="description"
                          class="<?= htmlspecialchars($descInput) ?>"><?= htmlspecialchars($descV) ?></textarea>

            </div>
            <div>
                <div class="lblPad"><label for="author">Author:</label></div>
                <input type="text" id="author" name="author" class="<?= htmlspecialchars($authorInput) ?>"
                       value="<?= htmlspecialchars($authorV) ?>"/>
            </div>
            <div>
                <div class="lblPad"><label for="link">Link:</label></div>
                <input type="text" id="link" name="link" class="<?= htmlspecialchars($linkInput) ?>"
                       value="<?= htmlspecialchars($linkV) ?>"/>
            </div>
            <div>
                <div class="lblPad"><label for="puDate">Published Date:</label></div>
                <input type="date" id="pubDate" name="pubDate" value="<?= htmlspecialchars($pubDV) ?>"/>
            </div>
            <br/>
            <div>
                <input type="submit" id="btn" name="submit" value="Submit!"/><br>
                <input type="submit" id="btn" name="sample" value="(Test) Populate!"/>
            </div>
            <div class="error">
                <ul>
                    <?php
                    if ($errorBlock != '') {
                        echo "ERRORS:";
                        echo $errorBlock;
                    }
                    ?>
                </ul>
            </div>
        </form>
    </div>
</div>
</body>
</html>