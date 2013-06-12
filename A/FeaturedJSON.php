<?php
require_once('../AutoLoader.php');


if ($_POST['action'] === 'delete') {
    // first delete associated users who RSVP'd to the event
    $query = sPDO::getInstance()->prepare('DELETE FROM featured_event_attendees WHERE featured_event_id = :event_id');
    $query->bindValue(':event_id', $_POST['eventId']);
    $query->execute();

    // now delete the actual event
    $query = sPDO::getInstance()->prepare('DELETE FROM featured_events WHERE featured_event_id = :event_id');
    $query->bindValue(':event_id', $_POST['eventId']);
    $query->execute();
}
if ($_POST['action'] === 'edit') {
    $query = sPDO::getInstance()->prepare('UPDATE featured_events SET '. $_POST['key'] . '= :value WHERE featured_event_id = :event_id');
    $query->bindValue(':value', str_replace('&', '&amp;', $_POST['value']));
    $query->bindValue(':event_id', $_POST['eventId']);
    $query->execute();  
}

if ($_POST['action'] === 'create') {
    //  print_r($_FILES['file']['tmp_name']);
    //  print_r($_FILES['file']['name']);
    //  print_r(count($_FILES['file']['tmp_name']));
//($_FILES["file"]["size"] / 1024 < 20000)
    if (true) {
        $no_errors = true;
        for ($i = 0; $i < count($_FILES['file']['error']); $i++) {
            if ($_FILES["file"]["error"][$i] > 0) {
                $no_errors = false;
                echo "Error Code: " . $_FILES["file"]["error"][$i] . "<br />";
            }
        } if ($no_errors) {

            echo "Upload: "; 
            print_r($_FILES["file"]["name"]);
            echo "<br />";
            echo "Type: ";
            print_r($_FILES["file"]["type"]);
            echo "<br />";
            echo "Temp file: ";
            print_r($_FILES["file"]["tmp_name"]);
            echo "<br />";

            $upload_succeeded = true;
            $markup = "";
            for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
                $location = "/Photos/Featured/" . $_FILES["file"]["name"][$i];
                $markup = $markup . " " . $location;

                if (!move_uploaded_file($_FILES["file"]["tmp_name"][$i], '/var/www' . $location))
                    $upload_succeeded = false;
                if(file_exists($location))
                    echo $_FILES["file"]["name"][$i] . " already exists. ";

            }
            
            if ($upload_succeeded) {
                print_r("hello" . $markup . "  this is the markup");
                echo "Stored in: " . "/var/www/Photos/Featured/" . $_FILES["file"]["name"];
                $query = sPDO::getInstance()->prepare('SELECT new_featured_event( :description, :startsAt, :endsAt, :location, :markup, :price, :headline, :is_private, :sub_headline, :host, :priority )');
                $query->bindValue(':description', str_replace('&', '&amp;', $_POST['description']));
                $query->bindValue(':startsAt', $_POST['startDate']);
                $query->bindValue(':endsAt', $_POST['endDate']);
                $query->bindValue(':location', $_POST['location']);
                $query->bindValue(':markup', $markup);
                $query->bindValue(':price', $_POST['price']);                
                $query->bindValue(':headline', str_replace('&', '&amp;', $_POST['headline']));
                $query->bindValue(':sub_headline', str_replace('&', '&amp;', $_POST['sub_headline']));
                $query->bindValue(':is_private', $_POST['is_private']);
                $query->bindValue(':host', $_POST['host']);
                 $query->bindValue(':priority', $_POST['priority']);






                if ($query->execute())
                    echo '<br/> SUCCESSFUL INSERTION INTO DATABASE';
            }
            else
                echo "Unable to store file";


            echo "<br /><a href=\"/A/Featured.php\">Go back to the featured event manager.</a>";
        }
    }
    else {
        echo "File too big. It should only be 400x200 pixels and under 2MB";
    }
}

if ($_POST['action'] === 'email') {
    require_once "mail.php";
    require_once "Mail/mime.php";

    $to = $_POST['toemail'];
    $subject = $_POST['subject'];

    //date_default_timezone_set("America/New_York");
    //$random_hash = md5(date('r', time()));
    $headers['From'] = '"The Socialer" <3ricob@gmail.com>';
    $headers['To'] = '<' . $to . '>';
    $headers['Subject'] = $subject;
    $headers['Reply-To'] = '"Eric O\'Brien" <3ricob@gmail.com>';
    //$headers['MIME-Version'] = '1.0';
    //$headers['Content-Type'] = 'multipart/alternative; boundary="PHP-alt-'.$random_hash.'"';
    //$headers['Content-Type'] = 'text/plain';
    //$headers["Return-path"] = "returnpath@address.com";

    $host = "ssl://smtp.googlemail.com";
    $port = "465";
    $username = "eric@thesocialer.com";
    $password = "social eric the";
    $smtp = Mail::factory('smtp', array('host' => $host,
                'port' => $port,
                'auth' => true,
                'username' => $username,
                'password' => $password));

    //define the html body of the message.
    ob_start(); //Turn on output buffering
    ?>
    <html>
        <head> 
            <title>The Socialer</title>
            <style type="text/css">
            </style>
        </head>
        <body>
    <?php echo $_POST['description']; ?>
        </body>
    </html>
    <?php
    //copy current buffer contents into $message variable and delete current output buffer
    $html = ob_get_clean();
    $text = $_POST['description'];

    $crlf = "\n";
    $mime = new Mail_mime(array('eol' => $crlf));

    $mime->setTXTBody($text);
    $mime->setHTMLBody($html);

    $message = $mime->get();
    $headers = $mime->headers($headers);

    //send the email
    $mail = $smtp->send($to, $headers, $message);

    if (PEAR::isError($mail)) {
        echo("<p>" . $mail->getMessage() . "</p>");
    } else {
        echo("Message successfully sent!");
    }
}
?>
