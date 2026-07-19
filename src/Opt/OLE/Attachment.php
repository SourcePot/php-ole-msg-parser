<?php
declare(strict_types=1);

namespace Opt\OLE;

class Attachment{
    private $attachment=[];

    function __construct(string $filename, string $mimeType, $content, $props=[])
    {
        $this->attachment = [
            'filename' => $filename,
            'mimeType' => $mimeType,
            'data'     => $content,
            'filesize' => strlen($content),
            'contentId' => $props['CONTENT_IDENTIFIER'] ?? hash('sha256',$content,FALSE,[])
        ];
    }

    public function contentId():string
    {
        return $this->attachment['contentId'];
    }
    public function filename():string
    {
        return $this->attachment['filename'];
    }
    
    public function contents()
    {
        return $this->attachment['data'];
    }

    public function contentType():string
    {
        return $this->attachment['mimeType'];
    }

    public function getAttachment():array
    {
        return $this->attachment;
    }

}
?>