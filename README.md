# ortic forum

This repository contains an incomplete forum solution for concrete5 v8+.

It's based on a technique presented at a concrete5 meetup.

Instead of using pages for everything, this forum uses a lean structure that should be more performant than a page +
attribute based solution. In order to handle the requests and still have clean urls, we are overriding the view method
in the page type controller. That way we can handle all requests in an elegant way and keep SEO friendly URLs.

## What's missing

* Validation
* Attachments
* Mail notifications
* Output improvements, show date of last message, number of messages per topic and so on 