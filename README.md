# php-ole-msg-parser

Minimal PHP library for parsing Outlook .msg files stored in OLE compound documents.
This packaged is derived from _koopa/php-ole-msg-parser_.

## Features

- Reads raw OLE compound streams without external dependencies
- Extracts headers, plain-text body, RTF body (as attachment) and attachments from .msg files
- Provides lightweight loader interfaces for custom property handling
- Test web page included: .../php-ole-msg-parser/src/www/index.php

## Installation

```bash
composer require sourcepot/php-ole-msg-parser
```

## Usage

```php
require __DIR__ . '/vendor/autoload.php';

$parser = new \Opt\OLE\MsgParser('path/to/message.msg');
$message = $parser->parse();

$headers = $message->headers;

$transportLayerRawHeaders=$headers['TRANSPORT_MESSAGE_HEADERS'];

$body = $message->body;

foreach ($message->attachments as $attachment){
    file_put_contents($attachment['filename'], $attachment['data']);
}        

```
## Requirements

- PHP 8.0+
- mbstring extenstion

## License

MIT