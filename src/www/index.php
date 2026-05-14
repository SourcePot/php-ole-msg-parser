<?php
/*
* This file is part of the Datapool CMS package.
* @package Datapool
* @author Carsten Wallenhauer <admin@datapool.info>
* @copyright 2023 to today Carsten Wallenhauer
* @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-v3
*/
declare(strict_types=1);

namespace SourcePot\Datapool\Foundation;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$html='<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Outlook Message Parser</title>
        <link type="text/css" rel="stylesheet" href="index.css"/>
    </head>
    <body>
        <div class="control">
        <h1>Outlook Message Parser</h1>
        <form name="892d183ba51083fc2a0b3d4d6453e20b" id="892d183ba51083fc2a0b3d4d6453e20b" method="post" enctype="multipart/form-data">
            <input type="file" name="msg_file" accept=".msg">
            <input type="submit" value="Parse MSG File">
        </form>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="index.js"></script>
    </body>
    </html>';

$msgFile = $_FILES['msg_file'] ?? null;
if (empty($msgFile)) {
    $html.="<p>No file uploaded. Please select a .msg file to parse.</p>";
} else if ($msgFile['error'] !== 0) {
    $html.="<p>There was an error uploading the file. Error code: ".$msgFile['error']."</p>";
} else if (pathinfo($msgFile['name'], PATHINFO_EXTENSION) !== 'msg') {
    $html.="<p>Invalid file type. Please upload a .msg file.</p>";
} else {
    require_once '../Opt/OLE/OleFile.php';
    require_once '../Opt/OLE/MsgParser.php';
    require_once '../RTF/StringScanner.php';
    require_once '../RTF/EmbeddedHTML.php';
    require_once '../RTF/CompressionCodec.php';

    if (!is_dir('../tests/')) {
        mkdir('../tests/');
    }
    $targetMsg = '../tests/message.msg';
    move_uploaded_file($msgFile['tmp_name'], $targetMsg);

    $parser = new \Opt\OLE\MsgParser($targetMsg);
    $message = $parser->parse();

    $html.='<h2 style="float:left;clear:both;">Header</h2>';
    
    $headerDiv='<div style="float:left;clear:both;border:1px dotted #000;padding:5px;margin:5px;width:auto;">';
    foreach ($message->headers as $key => $value){
        if ((is_object($value)?get_class($value):'') == 'DateTime'){
            $value = $value->format(DATE_RFC2822);
        }
        $headerDiv.='<p style="float:left;clear:both;margin:2px 0;">'.$key.': '.htmlentities(strval($value), ENT_QUOTES, 'UTF-8').'</p>';
    }

    $headerDiv.='</div>';
    $html.=$headerDiv;

    $html.='<h2 style="float:left;clear:both;">Text message</h2>';
    
    $html.='<p style="float:left;clear:both;">'.str_replace("\r\n", "<br>", $message->body).'</p>';

    $html.='<h2 style="float:left;clear:both;">Attachments</h2>';
    foreach ($message->attachments as $attachment){
        file_put_contents('../tests/'.$attachment['filename'],$attachment['data']);
        $data=$attachment['data'];
        unset($attachment['data']);
        $attachmentDiv='<div style="float:left;clear:none;border:1px dotted #000;padding:5px;margin:5px;width:auto;">';
        foreach($attachment as $key => $value){
            $attachmentDiv.='<p style="float:left;clear:both;margin:2px 0;">'.$key.': '.$value.'</p>';
        }
        $attachmentDiv.='</div>';
        $html.=$attachmentDiv;

    }

}
echo $html;
?>