#About

The EmbedVideo Extension is a MediaWiki extension which adds a parser function called #ev for embedding video clips from over 22 popular video sharing services in multiple languages and countries.

For more information about EmbedVideo, to download, to contribute, and to report bugs and problems, visit the GitHub project page:

https://github.com/Alexia/mediawiki-embedvideo

Issues, bug reports, and feature requests may be created at the issue tracker:

https://github.com/Alexia/mediawiki-embedvideo/issues

The MediaWiki extension page is located at:

https://www.mediawiki.org/wiki/Extension:EmbedVideo

##History

The original version of EmbedVideo was created by Jim R. Wilson.  That version was later forked by Mohammed Derakhshani as the EmbedVideoPlus extension.  In early 2010 Andrew Whitworth took over active maintenance of both extensions and merged them together as "EmbedVideo".  Much later on in September 2014 Alexia E. Smith forcefully took over being unable to contact a current maintainer.

The newer versions of EmbedVideo are intended to be fully backwards-compatible with both older EmbedVideo and EmbedVideoPlus extensions.

#License

EmbedVideo is released under the MIT license

http://www.opensource.org/licenses/mit-license.php

See LICENSE for more details

#Installation

##Download

There are three places to download the EmbedVideo extension. The first is directly from its GitHub project page, where active development takes place.  If you have git, you can use this incantation to check out a read-only copy of the extension source:

```
git clone https://github.com/Alexia/mediawiki-embedvideo.git
```

Downloadable archive packages for numbered releases will also be available from the github project page.

##Installation Instructions

1. Download the contents of the extension, as outlined above.
2. Create an EmbedVideo folder in the extensions/ folder of your MediaWiki installation.
3. Copy the contents of this distribution into that folder

For Mediawiki 1.19 through 1.23 add the following line to your LocalSettings.php:

```php
require_once("$IP/extensions/EmbedVideo/EmbedVideo.php");
```

For Mediawiki 1.24 and up add the following line to your LocalSettings.php:

```php
wfLoadExtension("EmbedVideo");
```

#Usage

## Tags

The EmbedVideo parser function expects to be called in any of the following ways:

### \#ev - Classic Parser Tag

* `{{#ev:service|id}}`
* `{{#ev:service|id|dimensions}}`
* `{{#ev:service|id|dimensions|alignment}}`
* `{{#ev:service|id|dimensions|alignment|description}}`
* `{{#ev:service|id|dimensions|alignment|description|container}}`
* `{{#ev:service|id|dimensions|alignment|description|container|urlargs}}`

However, if needed optional arguments may be left blank by not putting anything between the pipes:

* `{{#ev:service|id|||description}}`

### \#evt - Parser Tag for Templates

The \#evt parser tag allows for key=value pairs which allows for easier templating and readability.

    {{#evt:
    service=youtube
    |id=https://www.youtube.com/watch?v=pSsYTj9kCHE
    |alignment=right
    }}

### &lt;embedvideo&gt; - Tag Hook

Videos can easily be embedded with the &lt;embedvideo&gt;&lt;/embedvideo&gt; tag hook. The ID/URL goes as the input between the tags and parameters can be added as the tag arguments.

    <embedvideo service="youtube">https://www.youtube.com/watch?v=pSsYTj9kCHE</embedvideo>

## Attributes for Parser Tags

| Attribute                                   | Required | Default | Description                                                                                                                                                                                      |
|---------------------------------------------|----------|---------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `service="(See Supported Services below.)"` | yes      |         | The video service to call.                                                                                                                                                                       |
| `id="[id|url]"`                             | yes      |         | The raw ID of the video or URL from the player page.                                                                                                                                             |
| `dimensions="[WIDTH|WIDTHxHEIGHT|xHEIGHT]"` | no       | 640     | Dimensions in pixels to size the embed container. The standard format is width x height where either can be omitted, but the `x` must proceed height to indicate it as the height.<br/>Examples: `480`, `480x320`, `x320`. If the height is not provided it will be calculated automatically from the width and service default ratio.<br/>Some services such as *Gfycat* do not have standard heights and should be specified for each embed. `$wgEmbedVideoDefaultWidth` can be set in `LocalSettings.php` to override the default width. |
| `alignment="[left|center|right]"`           | no       | none    | Align the placement of the video either to the left, centered, or to the right.                                                                                                                  |
| `description="[wiki text]"`                 | no       | none    | Display a description under the embed container.                                                                                                                                                 |
| `container="[frame]"`                       | no       | none    | Specifies the container type to use for the embed.<br/>`frame`: Wrap the video player in a Mediawiki thumbnail box.                                                                                                                                     |
| `urlargs="modestbranding=1&version=3"`      | no       | none    | Allows extra URL arguments to be appended to the generated embed URL. This is useful for obscure options only supported on one service.                                                          |

## Examples

![Example \#1](EmbedVideoExample1.jpg "Example #1")

For example, a video from YouTube use the 'youtube' service selector enter either the raw ID:

    {{#ev:youtube|pSsYTj9kCHE}}

Or the full URL:

    {{#ev:youtube|https://www.youtube.com/watch?v=pSsYTj9kCHE}}

![Example \#2](EmbedVideoExample2.jpg "Example #2")

To display the same video as a right aligned large thumbnail with a description:

    {{#ev:youtube|https://www.youtube.com/watch?v=pSsYTj9kCHE|1000|right|Let eet GO|frame}}

For YouTube to have the video start at a specific time code utilize the urlargs(URL arguments) parameter. Take the rest of the URL arguments from the custom URL and place them into the urlargs. Please note that not all video services support extra URL arguments or may have different keys for their URL arguments.

    https://www.youtube.com/watch?v=pSsYTj9kCHE&start=76

    {{#ev:youtube|https://www.youtube.com/watch?v=pSsYTj9kCHE|||||start=76}}

## Supported Services

As of version 2.x, EmbedVideo supports embedding video content from the following services:

| Site                                                     | Service Name(s)                                                                       | ID Example                                                                            | URL Example(s)                                                                                                 |
|----------------------------------------------------------|---------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------|
| [Archive.org Videos](https://archive.org/details/movies) | `archiveorg`                                                                          | electricsheep-flock-244-80000-6                                                       | https://archive.org/details/electricsheep-flock-244-80000-6<br/>https://archive.org/embed/electricsheep-flock-244-80000-6                                                      |
| [Bambuser](http://bambuser.com/)                         | `bambuser` - Broadcasts                                                               | `bambuser_channel` - Channels                                                         | 5262334                                                                                                        |
| [Beam](https://beam.pro/)                                | `beam`                                                                                | RocketBear                                                                            | https://beam.pro/RocketBear                                                                                    |
| [Bing](http://www.bing.com/videos/)                      | `bing`                                                                                | 31ncp9r7l                                                                             | http://www.bing.com/videos/watch/video/adorable-cats-attempt-to-eat-invisible-tuna/31ncp9r7l                   |
| [Blip.tv](http://www.blip.tv/)                           | `blip` - Blip requires the full URL to the video page and does not accept the raw ID. |                                                                                       | http://blip.tv/vinylrewind/review-6864612                                                                      |
| [CollegeHumor](http://www.collegehumor.com/)             | `collegehumor`                                                                        | 6875289                                                                               | http://www.collegehumor.com/video/6875289/batman-says-his-goodbyes                                             |
| [Dailymotion](http://www.dailymotion.com/)               | `dailymotion`                                                                         | x1adiiw\_archer-waking-up-as-h-jon-benjamin\_shortfilms                               | http://www.dailymotion.com/video/x1adiiw\_archer-waking-up-as-h-jon-benjamin\_shortfilms                       |
| [Daum TVPot](http://tvpot.daum.net/)                     | `tvpot` - Obtain the URL or ID from the share menu URL.                               | s9011HdLzYwpLwBodQzCHRB                                                               | http://tvpot.daum.net/v/s9011HdLzYwpLwBodQzCHRB                                                                |
| [Div Share](http://www.divshare.com)                     | `divshare`                                                                            |                                                                                       |                                                                                                                |
| [Edutopia](http://edutopia.org)                          | Edutopia content moved to YouTube. Please use the youtube service selector below.     |                                                                                       |                                                                                                                |
| [FunnyOrDie](http://www.funnyordie.com/)                 | `funnyordie`                                                                          | c61fb67ac9                                                                            | http://www.funnyordie.com/videos/c61fb67ac9/to-catch-a-predator-elastic-heart-edition                          |
| [Gfycat](http://gfycat.com/)                             | `gfycat`                                                                              | BruisedSilentAntarcticfurseal                                                         | http://www.gfycat.com/BruisedSilentAntarcticfurseal                                                            |
| [Hitbox](http://www.hitbox.tv/)                          | `hitbox`                                                                              | Washuu                                                                                | http://www.hitbox.tv/Washuu                                                                                    |
| [Kickstarter](http://www.kickstarter.com/)               | `kickstarter`                                                                         | elanlee/exploding-kittens                                                             | https://www.kickstarter.com/projects/elanlee/exploding-kittens                                                 |
| [Metacafe](http://www.metacafe.com/)                     | `metacafe`                                                                            | 11404579                                                                              | http://www.metacafe.com/watch/11404579/lan\_party\_far\_cry\_4/                                                |
| [Nico Nico Video](http://www.nicovideo.jp/)              | `nico`                                                                                | sm24394325                                                                            | http://www.nicovideo.jp/watch/sm24394325                                                                       |
| [RuTube](http://rutube.ru/)                              | `rutube`                                                                              | b698163ccb67498db74d50cb0f22e556                                                      | http://rutube.ru/video/b698163ccb67498db74d50cb0f22e556/                                                       |
| [TeacherTube](http://teachertube.com)                    | `teachertube`                                                                         | 370511                                                                                | http://www.teachertube.com/video/thats-a-noun-sing-along-hd-version-370511                                     |
| [TED Talks](http://www.ted.com/talks/browse/)            | `ted`                                                                                 | bruce\_aylward\_humanity\_vs\_ebola\_the\_winning\_strategies\_in\_a\_terrifying\_war | http://www.ted.com/talks/bruce\_aylward\_humanity\_vs\_ebola\_the\_winning\_strategies\_in\_a\_terrifying\_war |
| [Tudou](http://www.tudou.com/)                           | `tudou`                                                                               | mfQXfumwiew                                                                           | http://www.tudou.com/listplay/mfQXfumwiew.html                                                                 |
| [Twitch](http://www.twitch.tv)                           | `twitch` - Live Streams                                                               | `twitchvod` - Archived Videos on Demand                                               | twitchplayspokemon                                                                                             |
| [Videomaten](http://89.160.51.62/recordme/spelain.htm)   | `videomaten`                                                                          |                                                                                       |                                                                                                                |
| [Vimeo](http://www.vimeo.com)                            | `vimeo`                                                                               | 105035718                                                                             | http://vimeo.com/105035718                                                                                     |
| [Vine](http://vine.co)                                   | `vine`                                                                                | h2B7WMtuX2t                                                                           | https://vine.co/v/h2B7WMtuX2t                                                                                  |
| [Yahoo Screen](http://screen.yahoo.com/)                 | `yahoo`                                                                               | katy-perry-dances-sharks-2015-024409668                                               | https://screen.yahoo.com/videos-for-you/katy-perry-dances-sharks-2015-024409668.html                           |
| [YouTube](http://www.youtube.com/)                       | `youtube` - Single Videos                                                             | `youtubeplaylist` - Playlists                                                         | pSsYTj9kCHE                                                                                                    |
| [Youku](http://www.youku.com/)                           | `youku`                                                                               | XODc3NDgzMTY4                                                                         | http://v.youku.com/v\_show/id\_XODc3NDgzMTY4.html                                                              |

#Configuration Settings

| Variable                  | Default Value | Description                                                                                                                                             |
|---------------------------|---------------|---------------------------------------------------------------------------------------------------------------------------------------------------------|
| $wgEmbedVideoMinWidth     |               | Integer - Minimum width of video players. Widths specified below this value will be automatically bounded to it.                                        |
| $wgEmbedVideoMaxWidth     |               | Integer - Maximum width of video players. Widths specified above this value will be automatically bounded to it.                                        |
| $wgEmbedVideoDefaultWidth |               | Integer - Globally override the default width of video players. When not set this uses the video service's default width which is typically 640 pixels. |

#Credits

The original version of EmbedVideo was written by Jim R. Wilson.  Additional major upgrades made by Andrew Whitworth, Alexia E. Smith, and other contributors.

See CREDITS for details