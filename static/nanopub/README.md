MicroPub support for Static Blog Engine
=======================================

This php script provides micropub support for Static Site Generators. Incoming posts are rewritten to a suitable front-matter format and saved to the user's content store.

Currently, the script will handle the following indieweb functions:-

- [notes](https://indieweb.org/note)
- [articles](https://indieweb.org/article)
- [replies](https://indieweb.org/reply), adding the **title** of the article/note you are replying to. Replies can be syndicated to external services.
- [photos](https://indieweb.org/note), note that this functionality _requires_ the use of JSON-posts. **nanopub** is not presently equipped to handle multipart uploads
- [checkins](https://indieweb.org/checkin), this functionality is heavily informed by [OwnYourSwarm](https://ownyourswarm.p3k.io/) and the format that service uses
- [bookmarks](https://indieweb.org/bookmark)
- [like-of](https://indieweb.org/like)
- [repost](https://indieweb.org/repost)

**nanopub** offers the following functionality as described in the formal [Micropub Specification](https://www.w3.org/TR/micropub/)

Required
--------
- Supports header and form parameter methods of authentication
- Supports creating posts using `x-www-form-urlencoded` syntax

Optional
--------
- Supports updating and deleting posts
- Supports JSON syntax and source content query
- Supports replacement and deletion of limited set of properties
- As it uses a separate Media Endpoint it provides configuration query

[Full implementation report](https://micropub.rocks/implementation-reports/servers/132/dohoQpnIdZYxrwcpMgzj) is available on [micropub.rocks](https://micropub.rocks/)

**nanopub** additionally supports syndication of content to external silos. Currently it provides syndication to [Twitter](https://twitter.com) and [Mastodon](https://mastodon.social), although it also provides a framework implementation for any modern API-based endpoint. An example is provided of the script pinging the [micro.blog](https://micro.blog) service to update the user's feed.

The code is self-explanatory and documented, and can be adjusted easily to meet different needs.

Installation
------------

Clone the contents into the `static` folder of your Hugo installation.

Since the 1.2 release, nanopub requires the use of [Composer](https://getcomposer.org/). Getting it to do all the things I wanted it to do was getting _way_ beyond my skill level. 

You'll need to set your site up with the requisite headers to:

- Use [IndieAuth](https://indieauth.com/setup) as an identity/token service
- Identify `nanopub.php` as your site's [micropub endpoint](https://indieweb.org/Micropub#How_to_implement)

On my site, these headers are provided as follows:

```html

<!-- indieweb components  -->
  <link rel="authorization_endpoint" href="https://indieauth.com/auth" />
  <link rel="token_endpoint" href="https://tokens.indieauth.com/token" />
  <link rel="micropub" href="https://ascraeus.org/nanopub.php" />

```

Then you'll have to configure the options in `configs.php`

- Twitter Keys can be obtained using the [Create New App](https://apps.twitter.com/app/new) on twitter.
- Mastodon Keys are more command-line driven, but relatively straightforward. See [the Mastodon API documentation](https://github.com/tootsuite/documentation/blob/master/Using-the-API/Testing-with-cURL.md)
- As Mastodon is a federated network, you do need to _explicitly_ specify your Mastodon Instance.
- If using the micro.blog function, you need to specify your site's rss/atom feed.
- To use the weather data, you'll need to add access data for a location-tracking endpoint (like a self-hosted version of [Compass](https://github.com/aaronpk/Compass)) and a DarkSky [API Key](https://darksky.net/dev/docs)
- Since 1.5, you can configure the frontmatter format for your posts. Currently the options are json, used in [Hugo](https://gohugo.io/content-management/front-matter/), or yaml, optionally used in Hugo, but required by other static engines such as [Jekyll](https://jekyllrb.com/docs/frontmatter/) or [Metalsmith](http://www.metalsmith.io). This could be used with other generators, such as [Pelican](docs.getpelican.com/en/stable/content.html), but the script would need to be changed to meet their specific format requirements.

Client Notes
------------
**nanopub** expects data inputs in accordance with the current (May 2017) Micropub Spec, and does not gracefully handled deprecated formats. In particular:

- `mp-slug`, not `slug` when setting the content of the slug property
- `mp-syndicate-to` not `syndicate-to` when setting syndication targets for POSSE

Any errors resulting from use of the deprecated formats are a matter for the client.

TODO
----
* [X] Save all content in the correct Hugo JSON format
* [X] Read all content from disk in correct microformats2 syntax
* [X] Make it work with a more complete set of micropub features
* [X] Reply-to
* [X] Like-Of
* [X] Repost-Of
* [ ] Make a `setup.php` script to complete the required configuration settings.
* [ ] Implement rsvp's, itineraries &c
* [ ] Trigger sitegen on succesful operation.

Author
---
* **Daniel Goldsmith** <https://ascraeus.org>

Licences
--------
- **nanopub** is released under the [Zero Clause BSD Licence](https://opensource.org/licenses/FPL-1.0.0) (0BSD).

Acknowledgments
---------------
* The IndieAuth validation sequence was taken from [Amy Guy's Minimal Micropub](https://rhiaro.co.uk/2015/04/minimum-viable-micropub), without which I couldn't have done this.
* All at the #indieweb and #indieweb-dev IRC channels, who provide inspiration and support in equal measure.
* [@lyda](https://phrye.com)

Changes
-------
Version | Date | Notes
-------:|:----:|:-----
1.5 | 2018-02-01 | Added configurable frontmatter, currently json or yaml
1.4 | 2018-01-26 | Extended for weather reporting, and rich-context likes/reposts
1.2 | 2018-01-02 | Expansion to include `repost` & `like` posts.
1.1 | 2017-11-14 | Rewrite of script to remove redundant and repetitive code.
|||FIX: Added a getallheaders() replacement for web servers without apache functions
1.0 | 2017-10-17 | First official release. 

 


>If someone is able to show me that what I think or do is not right, I will happily change, for I seek the truth, by which no one ever was truly harmed.  
_– Marcus Aurelius, Meditations, VI.21_
 
