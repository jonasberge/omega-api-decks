<?php

namespace Config;

use Config;
use Format\FormatDecoder;
use Format\FormatDecodeTester;
use Format\FormatEncoder;
use Format\NeedsRepository;
use Game\Deck;
use Game\MainDeck;
use Game\Repository\Repository;
use Game\Repository\SqliteRepository;
use Image\Image;
use Image\ImageCache;
use Image\ImageKey;
use Image\MemoryImage;
use Render\CellFactory;
use Render\Table;


abstract class CardRepository extends Repository
{
    private static ?Repository $instance = null;

    public static function get(): Repository
    {
        if (self::$instance === null) {
            $R = Config::get('repository');
            self::$instance = new SqliteRepository($R['path'], $R['options']);
        }

        return self::$instance;
    }
}


function get_repository(): Repository
{
    return CardRepository::get();
}

function create_repository_pdo(): \PDO
{
    $path = Config::get('repository')['path'];
    return new \PDO("sqlite:$path");
}


function create_encoder_from_class(string $class): FormatEncoder
{
    if (!is_subclass_of($class, FormatEncoder::class))
        throw new \Exception("$class is not a subclass of " . FormatEncoder::class);

    $args = [];
    if (is_subclass_of($class, NeedsRepository::class))
        $args[] = get_repository();

    return new $class(...$args);
}

function create_decoder_from_class(string $class): FormatDecoder
{
    if (!is_subclass_of($class, FormatDecoder::class))
        throw new \Exception("$class is not a subclass of " . FormatDecoder::class);

    $args = [];
    if (is_subclass_of($class, NeedsRepository::class))
        $args[] = get_repository();

    return new $class(...$args);
}

function create_decoder(string $format_name): FormatDecoder
{
    $D = Config::get('formats')['decoders'];
    if (!isset($D[$format_name]))
        throw new \Exception("no decoder exists for format '$format_name'");

    $class = $D[$format_name];
    return create_decoder_from_class($class);
}

function create_decoders(string ...$format_names): \Generator
{
    foreach ($format_names as $format_name)
        yield $format_name => create_decoder($format_name);
}

function create_all_decoders(): \Generator
{
    foreach (Config::get('formats')['decoders'] as $format_name => $class)
        yield $format_name => create_decoder_from_class($class);
}

function create_decode_tester(string ...$format_names): FormatDecodeTester
{
    $tester = new FormatDecodeTester();

    $decoders = count($format_names) > 0
        ? create_decoders(...$format_names)
        : create_all_decoders();

    foreach ($decoders as $format_name => $decoder)
        $tester->register($format_name, $decoder);

    return $tester;
}

function get_image_urls(ImageKey $key): array
{
    $lookup_json_path = Config::get('image_urls')['lookup_json_path'];
    $contents = file_get_contents($lookup_json_path);
    $lookup_table = json_decode($contents, true);

    $name = $key->value();
    if (!array_key_exists("$name", $lookup_table)) {
        return null;
    }

    return $lookup_table["$name"];
}

function image_loader(ImageKey $key, int $type, bool $allow_placeholder = true): ?Image
{
    $urls = get_image_urls($key);
    $image = null;

    foreach ($urls as $url) {
        try {
            $image = Image::from_url($url, $type);
            break;
        }
        catch (\Exception $e) {
            $image = null;
        }
    }

    if ($allow_placeholder && $image === null) {
        $placeholder = Config::get('images')['placeholder'];
        $image = MemoryImage::from_file($placeholder);
        if ($image === null)
            throw new \Exception("failed to read image placeholder");
    }

    return $image;
}

function create_image_cache(): ImageCache
{
    $cache = new ImageCache(
        Config::get('cache')['directory'],
        Config::get('cache')['subfolder_length']
    );

    $loader = \Closure::fromCallable(__NAMESPACE__ . '\\image_loader');

    $cache->loader($loader);
    $cache->type(\Image\ImageType::AUTO);

    return $cache;
}

function create_table(string $name, bool $is_center_deck): Table
{
    $tables = Config::get('tables');

    if (!isset($tables[$name]))
        throw new \Exception("table $name is not defined in configuration");

    $cell_dimensions = Config::get('cell');

    $cell_factory = new CellFactory(
        $cell_dimensions->width(),
        $cell_dimensions->height()
    );

    $T = $tables[$name];

    $table = new Table($T['width'], $T['height'], $cell_factory);

    $table->layout($T['layout']['primary'], $T['layout']['secondary']);
    $table->overlap($T['overlap']);

    $root_x = $T['root']->x();
    $root_y = $T['root']->y();
    if ($is_center_deck) {
        $root_x += $T['root_center_offset']->x();
        $root_y += $T['root_center_offset']->y();
    }
    $table->root($root_x, $root_y);
    $table->spacing($T['spacing']->horizontal(), $T['spacing']->vertical());

    return $table;
}

function create_deck_table(string $name, Deck $deck): Table
{
    $is_center_deck = $deck instanceof MainDeck && $deck->count() <= MainDeck::MIN_SIZE;
    $table = create_table($name, $is_center_deck);

    foreach ($deck->cards() as $card)
        $table->push($card);

    return $table;
}
