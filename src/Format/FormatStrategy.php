<?php

namespace Format;

use Game\Data\Repository;
use \Game\DeckList;


interface FormatEncodeStrategy
{
    function encode(DeckList $deck): string;
}

interface FormatDecodeStrategy
{
    function decode(string $encoded): ParsedCardList;
}
