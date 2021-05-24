
# Hugo live search using Fuse.js and Mark.js

This is not a standalone theme. This is a Hugo [theme component](https://gohugo.io/hugo-modules/theme-components/).
To use it:

1.  Clone this repo to `your-site/themes/hugo-search-fuse-js` directory.
    *Note that the theme name has to remain `hugo-search-fuse-js`.*

2.  Add "hugo-search-fuse-js" as the leftmost element of the `theme`
    list variable in your site's or theme's `config.toml`. Example:
    
    ``` example
    theme = ["hugo-search-fuse-js", "my-theme"]
    ```

3.  Create `content/search.md` with `layout` parameter set to "search",
    and `output` parameter set to `["html", "json"]`. The content or
    body of this `search.md` is not used by the `search.html` template
    which is part of this theme component.
    
    Here is an example `search.md`:
    
    ``` md
    +++
    title = "Search"
    layout = "search"
    outputs = ["html", "json"]
    [sitemap]
      priority = 0.1
    +++
    ```

# Requirements

For this theme component to work as-is:

1.  Your main theme **must** be structured using [base template and
    blocks](https://gohugo.io/templates/base/#define-the-base-template),
    and
2.  The `base` template needs to define `main` and `footer` blocks
    (shown in that previous link).

If the main theme does not meet these requirements, you will need to edit the
[`search.html`](https://github.com/kaushalmodi/hugo-search-fuse-js/blob/master/layouts/_default/search.html)
partial to make search work. You can find it in `layouts/_default/search.html` in this component.

## What if the main theme does not have `baseof.html`?

You have two options:

1. Edit the `search.html` to make it fit your theme.
2. Upgrade the theme to use the new "base template and blocks" approach.

## What if the main theme has `baseof.html`, but doesn't have the `main` and/or `footer` blocks?

It's very easy to add those blocks without breaking your existing theme.

To add the `main` block, add this to your `baseof.html` at the
appropriate place:

``` go-html-template
{{ block "main" . }}{{ end }}
```

And similarly add the below if the `footer` block is missing:

``` go-html-template
{{ block "footer" . }}{{ end }}
```

For an example, see the [`baseof.html` snippet in Hugo
docs](https://gohugo.io/templates/base/#define-the-base-template).

# Credits

 - Search functionality by [fuse.js](https://fusejs.io/).
 - Keyword highlighting by [mark.js](https://markjs.io/).
 - Initial Hugo implementation by [@eddiewebb](https://github.com/eddiewebb) in [this
gist](https://gist.github.com/eddiewebb/735feb48f50f0ddd65ae5606a1cb41ae).
 - Hugo theme component packaging by [@kaushalmodi](https://github.com/kaushalmodi).
 - jQuery removal and IE polyfill integration by [@lpar](https://github.com/lpar).
 - IE compatibility polyfills:
   - [Promises](https://github.com/taylorhakes/promise-polyfill)
   - [Fetch](https://github.com/github/fetch)
   - [URLSearchParams](https://github.com/ungap/url-search-params)
   - [Template and fragment API](https://github.com/webcomponents/template)
   - [Node.remove](https://stackoverflow.com/questions/20428877/javascript-remove-doesnt-work-in-ie)

