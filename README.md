# Theme variables

This theme can be tweaked using the following `params` variables in `config.toml`.

```
[params]
  Tagline = "My random notes"

  # Go date formats: https://golang.org/pkg/time/#pkg-constants
  DateForm = "Mon Jan 2, 2006"

  # https://github.com/poole/lanyon#themes
  ThemeColorBase = "8" # Valid values: "8", "9", "a", "b", "c", "d", "e", "f"

  defaultsection = "posts" # Use in Refined theme (layouts/_default/rss.xml)
```

## Isso comment support

To enable the Isso comment block, the `.Site.Params.isso` block needs to be present.

- `.Site.Params.isso.server` -- This is a mandatory variable. It must match the `[server] > listen` variable set in the `isso.conf` file (See [Isso Server Configuration][1]).
- `.Site.Params.isso.hidenamefield` -- Presence of this variable will hide the Name field.
- `.Site.Params.isso.hideemailfield` -- Presence of this variable will hide the Email field.
- `.Site.Params.isso.hidewebsitefield` -- Presence of this variable will hide the Website field.
- `.Site.Params.isso.tagline` -- This is an HTML string which is appended at the end of the Isso comment thread. Below example gives an idea of how this can be used.

```
[params]
  [params.isso] # Presence of .Site.Params.isso will enable Isso comments
    server = "localhost:1234" # Mandatory variable
    # hidenamefield = true # uncomment this line to hide the Name field
    # hideemailfield = true # uncomment this line to hide the Email field
    # hidewebsitefield = true # uncomment this line to hide the Website field
    tagline = """
            <p><small>Formatting Tips&mdash;
                      <b>**bold**</b>,
                      <i>*italics*</i>,
                      <code>`monospace`</code>,
                      <code>```</code>&#92;nmulti-line&#92;ncode&#92;n<code>```</code>
            </small></p>"""
```

[1]: https://posativ.org/isso/docs/configuration/server/
