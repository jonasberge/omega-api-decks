<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Decks</title>
    <link rel="stylesheet" type="text/css" href="./styles/index.css" />
    <link rel="icon" type="image/ico" href="./favicon.ico" />
  </head>
  <body>
    <script type="text/javascript">
      document.body.classList.add('invisible');
    </script>
    <div id="main">
      <div id="input" style="position: relative; height: auto">
        <form action="javascript:void(0);">
          <label class="item indent" for="deck-list">
            Paste your deck list or write it down
          </label>
          <div id="text-box">
            <textarea
              id="deck-list"
              class="item border input"
              name="deck-list"
              spellcheck="false"
              rows="10"
            ></textarea>
            <input
              type="button"
              name="submit"
              id="submit"
              class="border"
              value="Convert"
            />
          </div>
        </form>
      </div>
      <div id="output" class="item hidden">
        <div id="navigation">
          <label id="label-image" for="show-image" class="border selected">
            Image
          </label>
          <label id="label-convert" for="show-convert" class="border">
            Codes
          </label>
          <label id="label-error" for="show-error" class="border">Error</label>
        </div>
        <div class="tab">
          <input
            type="radio"
            name="select-tab"
            id="show-image"
            class="hidden"
            checked=""
          />
          <div class="bind-radio" id="output-image">
            <a id="deck-image-link" target="_blank" href="">
              <img id="deck-image" src="" />
            </a>
          </div>
        </div>
        <div class="tab">
          <input
            type="radio"
            name="select-tab"
            id="show-convert"
            class="hidden"
          />
          <div class="bind-radio" id="output-convert">
            <section>
              <label class="border" for="output-omega">Omega</label>
              <textarea id="output-omega" class="border" readonly></textarea>
            </section>
            <section>
              <label class="border" for="output-ydke">YDKe</label>
              <textarea id="output-ydke" class="border" readonly></textarea>
            </section>
            <section>
              <label class="border" for="output-ydk">YDK</label>
              <textarea id="output-ydk" class="border" readonly></textarea>
            </section>
            <section>
              <label class="border" for="output-names">Names</label>
              <textarea id="output-names" class="border" readonly></textarea>
            </section>
            <section>
              <label class="border" for="output-json">JSON</label>
              <textarea id="output-json" class="border" readonly></textarea>
            </section>
          </div>
        </div>
        <div class="tab">
          <input
            type="radio"
            name="select-tab"
            id="show-error"
            class="hidden"
          />
          <div class="bind-radio">
            <section>
              <label class="border error" for="output-error">
                Error
                <span class="separator hidden">&mdash;</span>
                <span class="content"></span>
              </label>
              <textarea id="output-error" class="border" readonly></textarea>
            </section>
          </div>
        </div>
      </div>
    </div>
    <footer class="noselect">
      <span>
        <span>powered by</span>
        <a href="https://github.com/vonas/omega-api-decks">
          vonas/omega-api-decks
        </a>
      </span>
    </footer>
    <div id="data-request-token" style="display:none;" data-request-token="<?php
        if (!in_array(strtolower(getenv('REQUEST_TOKEN_IN_UI')), ['0', 'false', 'no'])) {
            echo getenv('REQUEST_TOKEN');
        }
    ?>"></div>
    <script
      type="text/javascript"
      src="./vendor/cookie.js/cookie.umd.min.js"
    ></script>
    <script type="text/javascript" src="./scripts/lib.js"></script>
    <script type="text/javascript" src="./scripts/index.js"></script>
  </body>
</html>
