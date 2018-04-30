# ortic forum

This repository contains a simple forum solution for concrete5 v8+ and PHP 7+.

Instead of using pages for everything, this forum uses a lean structure that should be more performant than a page +
attribute based solution. Each topic is a page in concrete5, but the actual messages are stored in dedicated tables to make things faster and easier to handle.

## Installation

Copy the content of this repository to your packages directory and save everything in a folder called `ortic_forum`.
Install the package in the concrete5 UI. This will create a new page type called `Forum`. Simply create a new page of that type to add a forum to your sitemap. Feel free to create as many forums as you want

## Configuration

You can customize the forum by copying `config/ortic_forum.php` to `application/config/ortic_forum/ortic_forum.php`.

* `admin_group` The group of users that has the permission to update and delete forum messages of others.
* `attachment_fileset_name` The name of the fileset to which the files from the forum will be added.

## Customizing output

The forum uses single pages to render its output. Make sure you properly implement `view.php` in your theme. The forum will be rendered where `view.php` echo's `$innerContent`.

If that isn't enough, copy the content of https://github.com/ortic/concrete5-forum/blob/master/single_pages/forum.php to `application/single_pages/forum.php` and amend whatever you'd like to change. The same works for `topic.php`.

If that isn't enough, create a file called `forum.php` in your theme. Include the header, footer and whatever else you include from your `view.php`. Instead of echoing the content of `$innerContent`, use the content of https://github.com/ortic/concrete5-forum/blob/master/single_pages/forum.php and change whatever you like.
