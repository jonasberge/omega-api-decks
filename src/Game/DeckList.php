<?php

namespace Game;


class DeckList
{
    const DECK_COUNT = 3;

    public MainDeck  $main;
    public ExtraDeck $extra;
    public SideDeck  $side;

    public function __construct()
    {
        $this->main  = new MainDeck();
        $this->extra = new ExtraDeck();
        $this->side  = new SideDeck();
    }

    public function get(int $deck_type): Deck
    {
        switch ($deck_type) {
        case DeckType::MAIN:  return $this->main;
        case DeckType::EXTRA: return $this->extra;
        case DeckType::SIDE:  return $this->side;
        case DeckType::UNKNOWN: return null;
        }

        throw new \InvalidArgumentException("deck type does not exist");
    }

    public function decks(): \Generator
    {
        yield $this->main;
        yield $this->extra;
        yield $this->side;
    }

    public function cards(): \Generator
    {
        foreach ($this->decks() as $deck)
            foreach ($deck->cards() as $card)
                yield $card;
    }

    public function unique_cards(): \Generator
    {
        $encountered = [];

        foreach ($this->cards() as $card) {
            $code = $card->code();
            if (isset($encountered[$code]))
                continue;
            $encountered[$code] = true;
            yield $card;
        }
    }

    public function card_codes(): \Generator
    {
        foreach ($this->cards() as $card)
            yield $card->code();
    }

    public function unique_card_codes(): \Generator
    {
        foreach ($this->unique_cards() as $card)
            yield $card->code();
    }

    public function validate(bool $allow_too_little = false): void
    {
        foreach ($this->decks() as $deck)
            $deck->validate($allow_too_little);
    }
}
